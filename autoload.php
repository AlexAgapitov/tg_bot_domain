<?php

function auto($class_name)
{
    if (false === file_exists($path = __DIR__ . '/Application/' . implode('/', explode("\\", $class_name)) . '.php')) {
        return false;
    }
    include $path;
}
spl_autoload_register('auto');

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();