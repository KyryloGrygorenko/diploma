<?php
error_reporting(E_ALL);

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__ . DS . '..' . DS); // ../
define('WEBROOT', __DIR__ . DS );
define('VIEW_DIR', ROOT . 'View' . DS);
define('CONF_DIR', ROOT . 'Config' . DS);
define('VENDOR_DIR', ROOT . 'vendor' . DS);

require_once VENDOR_DIR . 'autoload.php';

spl_autoload_register(function($className) {
    $file = ROOT . str_replace('\\', DS, "{$className}.php");
    
    if (!file_exists($file)) {
        throw new \Exception("{$file} not found");
    }
    
    require_once $file;
});

try {
    \Library\Session::start();
    $cartService = new \Library\CartService();
    
    $config = new \Library\Config();

    $request = new \Library\Request();
    $router = new \Library\Router(ROOT . 'Config' . DS . 'routes.php');
    
    $dbConfig = [
        'host' => $config->getParameter('database_host'),
        'user' => $config->getParameter('database_user'),
        'dbname' => $config->getParameter('database_name'),
        'pass' => $config->getParameter('database_password'),
    ];
   
    $pdo = new \PDO('mysql: host=' . $dbConfig['host'] . '; dbname=' . $dbConfig['dbname'], $dbConfig['user'], $dbConfig['pass']);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    
    $container = new \Library\Container();
    $container->set('router', $router);
    $container->set('db_connection', $pdo);
    $container->set('repository', (new \Library\RepositoryManager())->setPdo($pdo));
    $container->set('cart_service', $cartService);

    $router->match($request);

    $route = $router->getCurrentRoute();
    
    $controller = 'Controller\\' . $route->controller . 'Controller';
    $action = $route->action . 'Action';

    $controller = (new $controller())->setContainer($container); // Controller\DefaultController

    if (!method_exists($controller, $action)) {
        throw new Exception("{$action} not found");
    }
    
    echo $controller->$action($request);
    
} catch (\Library\Exception\AccessDeniedException $e) {
    $controller = (new \Controller\ExceptionController)->setContainer($container);
    \Library\Controller::setDefaultLayout();
    echo $controller->handleAction($request, $e);
} catch (\Exception $e) {
    $controller = (new \Controller\ExceptionController)->setContainer($container);
    echo $controller->handleAction($request, $e);
}

