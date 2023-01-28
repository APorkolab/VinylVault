<?php

require dirname(__DIR__) . "/vendor/autoload.php";

use ErrorHandler;
use Dotenv\Dotenv;

set_error_handler([ErrorHandler::class, "handleError"]);
set_exception_handler([ErrorHandler::class, "handleException"]);

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

header("Content-type: application/json; charset=UTF-8");