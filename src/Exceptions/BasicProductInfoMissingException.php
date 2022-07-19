<?php

namespace App\Exceptions;

class BasicProductInfoMissingException extends \Exception
{
    protected $message = "Basic product information is required.";
}