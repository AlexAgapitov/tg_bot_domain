<?php
require __DIR__ . '/../../vendor/autoload.php';

$APPLICATION_CONFIG = include_once __DIR__ . '/../Core/config.php';

$bot_api_key  = $APPLICATION_CONFIG['bot_api_key'];
$bot_username  = $APPLICATION_CONFIG['bot_username'];

$mysql_credentials = [
    'host'     => 'mysql',
    'port'     => 3306, // optional
    'user'     => 'root',
    'password' => 'Sup3RS3curePassw0rd69',
    'database' => 'telegram_bot',
];

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    // Enable MySQL
    $telegram->enableMySql($mysql_credentials);

    // Handle telegram getUpdates request
    $telegram->handleGetUpdates();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // log telegram errors
     echo $e->getMessage();
}