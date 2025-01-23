<?php

namespace Domain\Commands;


use Core\Api;
use Longman\TelegramBot\Commands\UserCommand;

class ListCommand extends UserCommand
{
    protected $name = 'list';
    protected $description = 'Команда для получения списка добавленных доменов';
    protected $usage = '/list';
    protected $version = '1.0.0';
    protected $need_mysql = false;

    public function execute(): \Longman\TelegramBot\Entities\ServerResponse
    {
        $message = $this->getMessage();
        $chat = $message->getChat();
        $user = $message->getFrom();
        $chat_id = $chat->getId();
        $user_id = $user->getId();

        $params['user_id'] = $user_id;

        $Api = new Api();
        $domains = $Api->getDomains($params);

        $message = null;

        if ($Api->getRequest()['status'] !== 200 || null === $domains) {
            $message = "Ошибка! Попробуйте позже.";
        } else {
            if (!empty($domains)) {
                $message = "Список Ваших доменов:" . PHP_EOL;

                foreach ($domains AS $domain) {
                    $message .= PHP_EOL . $domain['name'];
                }

            } else {
                $message = "У Вас не добавлено доменов. Чтобы добавить момент введите /add";
            }
        }

        return $this->replyToChat($message);

    }
}