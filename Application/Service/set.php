<?php
// Load composer
require __DIR__ . '/../../vendor/autoload.php';

$APPLICATION_CONFIG = include_once __DIR__ . '/../Core/config.php';

$bot_api_key  = $APPLICATION_CONFIG['bot_api_key'];
$bot_username  = $APPLICATION_CONFIG['bot_username'];
$hook_url     = 'https://aga-tg-bots.ru/Service/hook.php';

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