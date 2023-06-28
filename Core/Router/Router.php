<?php

namespace Core\Router;

use App\Models\User;
use Core\Calls\Override;
use Core\Http\Res;
use Core\Interfaces\Router as InterfacesRouter;
use Core\Request;
use Router\Route;

/**
 * Router
 * 
 * PHP version 7.4.8
 * 
 * MVC Framework Developed By Bruiz
 */

class Router extends Override implements InterfacesRouter
{
    /**
     * Routing Table to store Routes
     * 
     * @return array
     */
    protected static $routes = [];
    protected static $previouslyCalled;

    /**
     * Get matched Params
     * 
     * @return array
     */
    protected static $params = [];

    /**
     * Get type of route
     */
    protected $method = 'GET';

    protected static $guarded;

    // private static $

    // public function __construct($route = '', $param = '', $method = '')
    // {
    //     if ($route !== '')
    //         $this->add($route, $param, $method);
    // }

    /**
     * Create add route method to store in Routing table
     * 
     * @param string $route the routing route url
     * @param array $param Prameters(controllers and action)
     * 
     * @return Router
     */
    public static function add($route, $param = [], $method = 'get'): Router
    {
        // Convert route to a regular expression.. escape slashes (/ = \/)
        $route = preg_replace('/\//', '\\/', $route);

        // Convert route to Variables: {controller}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        // Convert Variables with custom regular expression: {id:\d+}
        $route = preg_replace('/\{([a-z-]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        // Add start and end delimeter and case insensitive flag
        $route = '/^' . $route . '$/i';

        // $class = new static;

        self::$routes[$route][$method]['method'] = $param;
        self::$previouslyCalled = [
            'method' => $method,
            'route' => $route,
        ];
        // $class->method = "IOIIOI";
        return new static;
    }

    /**
     * Get Route
     *  
     */
    public static function get(string $route, $args = []): Router
    {
        return Router::add($route, $args, 'get');
    }

    public static function post(string $route, $args = []): Router
    {
        return Router::add($route, $args, 'post');
    }
    public static function put(string $route, $args = ''): Router
    {
        return Router::add($route, $args, 'put');
    }
    public static function delete(string $route, $args = ''): Router
    {
        return Router::add($route, $args, 'delete');
    }

    public static function group($route, $groups)
    {
        array_map(function () use ($route) {
        }, $groups);
    }

    public function guard($guard = "none")
    {
        // self::$guarded = $guard;
        $previouslyCalledRoute = self::$previouslyCalled['route'];
        $previouslyCalledMethod = self::$previouslyCalled['method'];
        // Res::json([

        //     $previouslyCalledRoute, $previouslyCalledMethod
        //     , self::$previouslyCalled,
        //     'routes' => self::$routes,
        // ]);
        self::$routes[$previouslyCalledRoute][$previouslyCalledMethod]['access'] = $guard;
        // Res::send([
        //     'routes' => self::$routes,
        //     'groups' => self::$guarded
        // ]);
    }

    /**
     * Get routes
     * 
     * @return void
     */
    public function getRoutes()
    {
        return self::$routes;
    }

    /**
     * Match the requested url to the routes
     * 
     * @param string $url the requested url
     * 
     * @return boolean
     */
    public function match($url, $method)
    {
        foreach ($this::$routes as $routes => $params) {
            if (preg_match($routes, $url, $matches)) {

                foreach ($matches as $key => $value) {

                    if (is_string($params[$method]['method'])) :

                        $ct = $this->controller_action_str($params[$method]['method']);

                        $params[$method]['method'] = ['binding' => [
                            'controller' => $ct->controller,
                            'action' => $ct->action,
                        ]];
                    else :
                        $params[$method]['method']['binding']['access'] = $params[$method]['access'];

                    endif;
                    if (is_string($key)) {
                        $params[$method]['method']['binding'][$key] = $value;
                    }
                }

                // Res::send($params[$method]['method']);
                // $ct = $this->controller_action_str($params[$method]['method']);

                self::$params = $params[$method]['method'];
                return true;
            }
        }
        return false;
    }

    /**
     * Get Matched routes to the Param
     * 
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Dispatch the route creating the controller object and action method
     * 
     * @param string $url the matched Url
     * 
     * 
     * @return void
     */
    public function dispatch($url)
    {
        $reqMethod = $_SERVER['REQUEST_METHOD'];
        $url = $this->removeQueryString($url);
        if ($this->match($url, strtolower($reqMethod))) {
            $params = self::$params;

            if (is_string($params)) {
                $ct = $this->controller_action_str($params);
                $controller = $ct->controller;
                $action = $ct->action;
            }

            if (is_array($params)) {
                $arr = $this->call_array($params);
                $controller = $arr['controller'];
                $action = $arr['action'];
            }

            if (class_exists($controller ?? '')) {

                $controller_obj = new $controller($this::$params['binding'] ?? [], self::$guarded);
                // $controller_obj->guard($this->guarded);

                $action = $this->convertToCamelCase($action);

                if (is_callable([$controller_obj, $action])) {
                    if ($reqMethod == 'GET') :
                        $controller_obj->$action(Request::get());
                    elseif ($reqMethod == 'POST') :
                        $controller_obj->$action(Request::post());
                    elseif ($reqMethod == 'PUT') :
                        $controller_obj->$action(Request::put());
                    elseif ($reqMethod == 'DELETE') :
                        $controller_obj->$action(Request::delete());
                    endif;
                } else {
                    throw new \Exception("Method $action in Controller class $controller not Found");
                }
            } else if (is_callable($params)) {
                return call_user_func($params);
            } else {
                throw new \Exception("Controller class $controller dosent exist");
            }
        } else {
            throw new \Exception('Page not Found', 404);
        }
    }

    public function call_array(array $params = [])
    {
        $class = isset($params[0]) ? $params[0] : null;
        $method = isset($params[1]) ? $params[1] : null;

        if (isset($params['binding'])) :
            extract($params['binding']);
            $class = $controller ?? $class;
            $method = $action ?? $method;
        endif;

        return [
            'controller' => $class,
            'action' => $method,
        ];
    }

    public function controller_action_str($param)
    {
        $paramExp = explode('@', $param);
        $controller = $this->convertTostudlyCaps($paramExp[0]);
        $controller = $this->getNamespace(isset($paramExp[2]) ? $paramExp[2] : '') . $controller;

        return (object) [
            'controller' => $controller,
            'action' => $this->convertToCamelCase($paramExp[1])
        ];
    }

    /**
     * Convert string to studly caps e.g post-man = PostMan
     * 
     * @param string $string string to convert
     * 
     * @return string
     */
    protected function convertToStudlyCaps($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * Convert string to Camelcase e.g post-man = postMan
     * 
     * @param string $string string to convert
     * 
     * @return string
     */
    protected function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    /**
     * Remove the query string variables from the URL (if any). As the full
     * query string is used for the route, any variables at the end will need
     * to be removed before the route is matched to the routing table. For
     * example:
     *
     *   URL                           $_SERVER['QUERY_STRING']  Route
     *   -------------------------------------------------------------------
     *   localhost                     ''                        ''
     *   localhost/?                   ''                        ''
     *   localhost/?page=1             page=1                    ''
     *   localhost/posts?page=1        posts&page=1              posts
     *   localhost/posts/index         posts/index               posts/index
     *   localhost/posts/index?page=1  posts/index&page=1        posts/index
     *
     * A URL of the format localhost/?page (one variable name, no value) won't
     * work however. (NB. The .htaccess file converts the first ? to a & when
     * it's passed through to the $_SERVER variable).
     *
     * @param string $url The full URL
     *
     * @return string The URL with the query string variables removed
     */

    protected function removeQueryString($url)
    {
        $url = explode('&', $url);
        if (strrpos($url[0], '=') == false) {
            $url = $url[0];
        }
        return $url;
    }

    /**
     * Get namespace for the controller class. The namespace
     * defined in the route parameter is added if present
     * 
     * @return string The request URL
     */
    protected function getNamespace($name = '')
    {
        $namespace = $name == '' ? 'App\Controllers\\' : 'App\Controllers\\' . ucwords($name) . '\\';
        if (is_array(self::$params))
            if (array_key_exists('namespace', self::$params))
                $namespace .= ucwords(self::$params['namespace']) . '\\';
        return $namespace;
    }
}
