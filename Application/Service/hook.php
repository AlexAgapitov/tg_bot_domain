<?php
function auto($class_name)
{
    if (false === file_exists($path = __DIR__ . '/' . implode('/', explode("\\", $class_name)) . '.php')) {
        return false;
    }
    include $path;
}
spl_autoload_register('auto');
require __DIR__ . '/../../vendor/autoload.php';

error_reporting(E_ALL); // Error/Exception engine, always use E_ALL

ini_set('ignore_repeated_errors', TRUE); // always use TRUE

ini_set('display_errors', FALSE); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment

ini_set('log_errors', TRUE); // Error/Exception file logging engine.
ini_set('error_log', __DIR__.'/errors.log'); // Logging file path

$APPLICATION_CONFIG = include_once __DIR__ . '/../Core/config.php';

$bot_api_key  = $APPLICATION_CONFIG['bot_api_key'];
$bot_username  = $APPLICATION_CONFIG['bot_username'];

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    $telegram->addCommandsPaths($APPLICATION_CONFIG['commands']['paths']);

    // Handle telegram webhook request
    $telegram->handle();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!
    // log telegram errors
    // echo $e->getMessage();
}