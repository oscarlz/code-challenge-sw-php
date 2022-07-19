<?php

namespace App\Exceptions;

class MethodNotAllowedException extends \Exception
{
    protected $message = 'Método no permitido.';
}