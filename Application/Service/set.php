<?php
require __DIR__ . '/../../autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

$config = include_once __DIR__ . '/../Core/config.php';

$bot_api_key  = $config['bot_api_key'];
$bot_username = $config['bot_username'];
$hook_url     = $config['bot_hook_url'];

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