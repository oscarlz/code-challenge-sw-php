<?php
require_once __DIR__ . "/vendor/autoload.php";

use App\Controllers\ApiController;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$api = new ApiController();
$api->handleRequest($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);