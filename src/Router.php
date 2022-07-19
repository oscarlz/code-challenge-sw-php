<?php

namespace App;

use App\Exceptions\RouteNotFoundException;
use App\Exceptions\MethodNotAllowedException;

class Router
{
    private $routes = [];

    /**
     * Entrance point for registering new routes
     * 
     * @param string $route
     * @param array $action
     * @param string $requestMethod
     * 
     * @return string
     */
    public function register(string $route, array $action, string $requestMethod): self
    {
        $this->routes[$route] = ['action' => $action, 'method' => $requestMethod];

        return $this;
    }

    public function resolve(string $requestURI, string $requestMethod)
    {   
        $route = ($requestURI === '/scandiweb-backend') ? '/' : '/' . explode('/', $requestURI)[2];
        $action = $this->routes[$route]['action'] ?? null;

        if(!$action){
            throw new RouteNotFoundException();
        }

        if($this->routes[$route]['method'] !== $requestMethod){
            throw new MethodNotAllowedException();
        }

        if(is_array($action)){
            [$class, $method] = $action;

            if(class_exists($class)){
                $classObject = new $class();

                if(method_exists($classObject, $method)){

                    // check if there is any payload data in the request to send to the method
                    $params = $this->getRequestPayload();

                    // we decode the json and then transform the result to an array (second param of json_decode)
                    return call_user_func_array([$classObject, $method], ['params' => json_decode($params, true)]);
                }
            }
        }

        throw new RouteNotFoundException();
    }

    /**
     * Gets the payload from the request.
     * 
     * @return string
     */
    protected function getRequestPayload(): string
    {
        $putfp = fopen('php://input', 'r');
        $payload = fread($putfp, 1024);
        fclose($putfp);

        return $payload;
    }
}