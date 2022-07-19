<?php

namespace App\Exceptions;

class ServerErrorException extends \Exception
{
    protected $message = 'Something happened with the server. We will get in touch';

    public function __construct()
    {
        http_response_code(500);
    }
}