<?php

namespace App\Exceptions;

class RouteNotFoundException extends \Exception
{
    protected $message = '404 No encontrada.';
    
    public function __construct()
    {
        http_response_code(404);
    }
}