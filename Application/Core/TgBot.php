<?php

namespace Core;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class TgBot
{
    public static function sendMessage(int $user_id, string $message)
    {
        try {
            $config = require __DIR__ . '/config.php';
            $telegram = new Telegram($config['bot_api_key'], $config['bot_username']);
            $telegram->enableMySql($config['mysql']);

            Request::initialize($telegram);

            $data = ['chat_id' => $user_id, 'text' => $message];
            if (!empty($keyboard))
                $data['reply_markup'] = $keyboard;

            Request::sendMessage($data);
        } catch (\Exception $e) {
            //Silence is golden
//            var_dump($e);
        }
    }
}