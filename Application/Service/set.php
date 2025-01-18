<?php
// Load composer
require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

$APPLICATION_CONFIG = include_once __DIR__ . '/../Core/config_tg.php';

$bot_api_key  = $APPLICATION_CONFIG['bot_api_key'];
$bot_username = $APPLICATION_CONFIG['bot_username'];
$hook_url     = $APPLICATION_CONFIG['bot_hook_url'];

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    // Set webhook
    $result = $telegram->setWebhook($hook_url);
    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // log telegram errors
    // echo $e->getMessage();
}