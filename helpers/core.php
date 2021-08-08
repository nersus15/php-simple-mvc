<?php

/**
 * @author Fathurrahman
 */
class UrlParser
{
    private $controller = "", $method = "", $param = [], $config = [];
    function __construct()
    {
        $this->config = (new josegonzalez\Dotenv\Loader('./.env'))->parse()->toArray();
    }
    function read_url()
    {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return $url;
    }

    /**
     * @param Bool $return
     */
    function parse_url($return = true)
    {
        var_dump(__DIR__);die;
        $url = $this->read_url();
        $this->config = (new josegonzalez\Dotenv\Loader('./.env'))->parse()->toArray();
        $clear_url = str_replace($this->base_url(), '', $url);
        // if(stristr($url, 'localhost')){
        // }
        $segments = explode('/', $clear_url);
        if ($segments[0] == "")
            unset($segments[0]);

        if (count($segments) == 0) {
            $this->controller = $this->config['DEFAULT_CONTROLLER'];
            $this->method = $this->config['DEFAULT_METHOD'];
        } elseif (count($segments) == 1) {
            $this->controller = $segments[0];
            $this->method = $this->config['DEFAULT_METHOD'];
        } elseif (count($segments) == 2) {
            $this->controller = $segments[0];
            $this->method = $segments[1];
        } elseif (count($segments) > 2) {
            $this->controller = $segments[0];
            $this->method = $segments[1];
            unset($segments[0], $segments[1]);
            $this->param = $segments;
        }
        
    }
    /**
     * @param String $key - Data yang ingin diambil ['controller', 'method', 'parameter'], jika kosong akan mengembalikan semuanya dalam bentuk stdClass
     * @return Array
     */
    function get_parsed_url($key = null)
    {
        if (!empty($key) && !in_array($key, ['controller', 'method', 'parameter']))
            if (empty($key))
                return (object) ['controller' => $this->controller, 'method' => $this->method, 'params' => $this->param];
            else
                return ($key == 'controller' ? $this->controller : ($key == 'method' ? $this->method : $this->param));
    }

    function load_class($class, $className = null, $folder = 'controllers', $return = false){
        require_once './' . $folder . '/' . $class . '.php';
        $classObject = null;
        if(empty($className))
            $classObject = new $class;
        else
            $classObject = new $className;

        $this->{$class} = $classObject;

        if($return)
            return $classObject;
    }

    /**
     * @param String $uri
     */
    function base_url($uri = null)
    {
        return empty($uri) ? $this->config['BASE_URL'] . '/' : $this->config['BASE_URL'] . $uri;
    }
}
