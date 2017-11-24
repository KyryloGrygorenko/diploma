<?php

namespace Library;

class Config
{
    private $params = [];
    
    public function __construct()
    {
        // scandir + get all yml files in conf dir
        $this->params += \Symfony\Component\Yaml\Yaml::parse(file_get_contents(CONF_DIR . 'db.yml'));
    }
    
    // public function setParameter($key, $value)
    // {
    //     $this->params[$key] = $value;
    // }
    
    public function getParameter($key)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }
        
        return null;
    }
    
    public function addParametersArray(array $arr)
    {
        $this->params += $arr;
    }
}
