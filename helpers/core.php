<?php

/**
 * @author Fathurrahman
 */
require_once './helpers/query_builder.php';

class Core
{
    private $controller = "", $method = "", $param = [], $config = [];
    public $db;
    function __construct()
    {   
        $this->db = new QueryBuilder;
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
        $url = $this->read_url();
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

        $controller = $this->load_class('controllers/' . ucwords($this->controller), null, 'controllers', true);

        if (method_exists($controller, $this->method))
            call_user_func_array([$controller, $this->method], $this->param);
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

    function get_path($path = null)
    {
        $root = __DIR__;
        $isWindows = DIRECTORY_SEPARATOR != '/';
        $root = $isWindows ? str_replace('helpers', '', $root) : str_replace('helpers', '', $root);

        $path = $root . $path;

        if ($isWindows)
            $path = str_replace('/', '\\', $path);

        return $path;
    }

    function load_class($class, $className = null, $return = false)
    {
        require $this->get_path($class . '.php');
        $segments = explode('/', $class);
        $class = str_replace('.php', '', lastArr($segments));

        $classObject = null;
        if (empty($className))
            $classObject = new $class();
        else
            $classObject = new $className();

        $this->{$class} = $classObject;

        if ($return)
            return $classObject;
    }

    /**
     * @param String $uri
     */
    function base_url($uri = null)
    {
        return empty($uri) ? $this->config['BASE_URL'] . '/' : $this->config['BASE_URL'] . '/' . $uri;
    }

    function load_view($view, $params = [])
    {
        $html = null;
        try {

            ob_start();
            if (!empty($data))
                extract($data);
            include_once $this->get_path('views/' . $view . '.php');
            $html = ob_get_contents();
            ob_end_clean();
        } catch (\Throwable $th) {
            print_r($th);
        }
        echo $html;
    }
}
