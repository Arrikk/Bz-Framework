<?php

namespace Core;

/**
 * Router
 * 
 * PHP version 7.4.8
 * 
 * MVC Framework Developed By Bruiz
 */

class Router
{
    /**
     * Routing Table to store Routes
     * 
     * @return array
     */
    protected $routes = [];

    /**
     * Get matched Params
     * 
     * @return array
     */
    protected $params = [];

    /**
     * Get type of route
     */
    protected $method = 'GET';

    /**
     * Create add route method to store in Routing table
     * 
     * @param string $route the routing route url
     * @param array $param Prameters(controllers and action)
     * 
     * @return class
     */
    public function add($route, $param = [])
    {
        // Convert route to a regular expression.. escape slashes (/ = \/)
        $route = preg_replace('/\//', '\\/', $route);

        // Convert route to Variables: {controller}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        // Convert Variables with custom regular expression: {id:\d+}
        $route = preg_replace('/\{([a-z-]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        // Add start and end delimeter and case insensitive flag
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $param;
        $this->routes[$route]['method'] = 'GET';
        $this->method = $route;
        return $this;
    }

    /**
     * Get Route
     * 
     */
    public function get()
    {
        $this->routes[$this->method]['method'] = 'GET';
    }

    public function post()
    {
        $this->routes[$this->method]['method'] = 'POST';
    }
    public function put()
    {
        $this->routes[$this->method]['method'] = 'PUT';
    }
    public function delete()
    {
        $this->routes[$this->method]['method'] = 'DELETE';
    }

    /**
     * Get routes
     * 
     * @return void
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Match the requested url to the routes
     * 
     * @param string $url the requested url
     * 
     * @return boolean
     */
    public function match($url)
    {
        foreach ($this->routes as $routes => $params) {

            if (preg_match($routes, $url, $matches)) {

                foreach ($matches as $key => $value) {

                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }
                $this->params = $params;
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
     * @return void
     */
    public function dispatch($url)
    {
        $url = $this->removeQueryString($url);
        if ($this->match($url)) {
            $controller = $this->convertTostudlyCaps($this->params['controller']);
            $controller = $this->getNamespace() . $controller;

            if (class_exists($controller)) {

                $controller_obj = new $controller($this->params);

                $action = $this->convertToCamelCase($this->params['action'] ?? $this->params['controller']);

                if (is_callable([$controller_obj, $action])) {
                    if ($this->params['method'] == 'GET') :
                        $controller_obj->$action(Request::get());
                        elseif ($this->params['method'] == 'POST') :
                        $controller_obj->$action(Request::post());
                        elseif ($this->params['method'] == 'PUT') :
                        $controller_obj->$action(Request::put());
                        elseif ($this->params['method'] == 'DELETE') :
                        $controller_obj->$action(Request::delete());
                    endif;
                } else {
                    throw new \Exception("Method $action in Controller class $controller not Found");
                }
            } else {
                throw new \Exception("Controller class $controller dosent exist");
            }
        } else {
            throw new \Exception('Page not Found', 404);
        }
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
    protected function getNamespace()
    {
        $namespace = 'App\Controllers\\';
        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }
        return $namespace;
    }
}
