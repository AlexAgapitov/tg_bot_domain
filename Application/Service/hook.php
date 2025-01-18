<?php
require __DIR__ . '/../autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

$APPLICATION_CONFIG = include_once __DIR__ . '/../Core/config.php';

$bot_api_key  = $APPLICATION_CONFIG['bot_api_key'];
$bot_username  = $APPLICATION_CONFIG['bot_username'];

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    $telegram->addCommandsPaths($APPLICATION_CONFIG['commands']['paths']);

    $telegram->enableMySql($APPLICATION_CONFIG['mysql']);


    // Handle telegram webhook request
    $telegram->handle();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!
    // log telegram errors
    // echo $e->getMessage();
}