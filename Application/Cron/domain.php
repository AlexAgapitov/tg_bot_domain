<?php

error_reporting(E_ALL);

ini_set('display_errors', true);

require_once __DIR__.'/../autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

\Core\Router::checkPayDate();

