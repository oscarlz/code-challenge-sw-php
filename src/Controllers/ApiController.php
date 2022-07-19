<?php

namespace App\Controllers;

use App\Router;

class ApiController
{
    protected $router;

    public function __construct()
    {
        // instantiate router
        $this->router = new Router();
        $this->registerRoutes();

    }

    private function registerRoutes(): void
    {
        // register available routes
        $this->router->register('/get-products', [ProductController::class, 'listAction'], 'GET');
        $this->router->register('/delete-products', [ProductController::class, 'deleteProductsAction'], 'POST');
        $this->router->register('/add-product', [ProductController::class, 'createAction'], 'POST');
        $this->router->register('/check-sku', [ProductController::class, 'skuExistsAction'], 'POST');
    }

    /**
     * Handle request with the router and send ouput
     *
     * @param string $requestUri
     * @param string $requestMethod
     *
     */
    public function handleRequest($requestUri, $requestMethod)
    {
        $jsonData = $this->router->resolve($requestUri, $requestMethod);
        $this->sendOutput($jsonData);
    }

    /**
     * Send API output.
     *
     * @param json $data
     * @param array $httpHeader
     * 
     * Prints the json data with its proper HTTP headers.
     */
    protected function sendOutput($data, $httpHeaders = array()): string
    {
        header_remove('Set-Cookie');
        header('Content-Type: application/json');

        if(count($httpHeaders) === 0){
            
            // add default header response.
            header('HTTP/1.1 200 OK');
        }

        if (count($httpHeaders) > 0) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }
 
        echo $data;
        exit;
    }
}