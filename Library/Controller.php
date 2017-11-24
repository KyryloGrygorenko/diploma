<?php

namespace Library;

abstract class Controller
{
    protected $container;
    
    private static $layout = 'layout.phtml';
//    private static $admin = false;

    public static function setAdminLayout()
    {
        self::$layout = 'admin_layout.phtml';
    }
//    public static function setAdmin()
//    {
//        self::$admin = true;
//    }
    public static function setDefaultLayout()
    {
        self::$layout = 'layout.phtml';
    }
    
    public function setContainer(Container $container)
    {
        $this->container = $container;
        
        return $this;
    }
    
    public function get($key)
    {
        return $this->container->get($key);
    }
    
    public function checkAccess()
    {
        if (!Session::has('user')) {
            throw new Exception\AccessDeniedException('You are not allowed to access this', 403);
        }
    }
    
    // todo: make as public static function
    public function render($view, array $args = [])
    {
        extract($args);

        // todo: cleanup
        $dir = trim(str_replace(['\\', 'Controller'], [DS, ''], get_class($this)), '/');
        $file = VIEW_DIR . $dir . DS . $view;

        if (!file_exists($file)) {

            throw new \Exception("{$file} not found!!");
        }
        
        ob_start();
        require $file;
        $content = ob_get_clean();

        ob_start();
        require VIEW_DIR . self::$layout;
        return ob_get_clean();

    }
}