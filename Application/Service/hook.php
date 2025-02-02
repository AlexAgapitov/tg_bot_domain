<?php
require __DIR__ . '/../../autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

$config = include_once __DIR__ . '/../Core/config.php';

$bot_api_key  = $config['bot_api_key'];
$bot_username  = $config['bot_username'];

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    $telegram->addCommandsPaths($config['commands']['paths']);

    $telegram->enableMySql($config['mysql']);


    // Handle telegram webhook request
    $telegram->handle();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!
    // log telegram errors
    // echo $e->getMessage();
}