<?php

namespace Domain\Commands;

use Core\Api;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class AddCommand extends UserCommand
{
    protected $name = 'add';
    protected $description = 'Команда для добавления домена';
    protected $usage = '/add';
    protected $version = '1.0.0';
    protected $need_mysql = false;

    private Api $Api;

    public function __construct(Telegram $telegram, ?Update $update = null)
    {
        parent::__construct($telegram, $update);
        $this->Api = new Api();
    }

    public function execute(): \Longman\TelegramBot\Entities\ServerResponse
    {
        $message = $this->getMessage();
        $chat = $message->getChat();
        $user = $message->getFrom();
        $text = trim($message->getText(true));
        $chat_id = $chat->getId();
        $user_id = $user->getId();

        $data = [
            'chat_id' => $chat_id,
            'reply_markup' => Keyboard::remove(['selective' => true]),
        ];

        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

        $notes = &$this->conversation->notes;
        !is_array($notes) && $notes = [];

        $state = $notes['state'] ?? 0;

        $result = Request::emptyResponse();

        switch ($state) {
            case 0:
                if ($text === '') {
                    $notes['state'] = 0;
                    $this->conversation->update();

                    $data['text'] = 'Введите домен:';

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['name'] = $text;
                $text = '';
            case 1:
                $res = $this->execApiFunc('getTimes', [], $error);
                $keyboard = array_column($res, 'name');
//                $keyboard = ['11:00 - 12:00', '12:00 - 13:00'];
                if ($text === '' || !in_array($text, $keyboard, true)) {
                    $notes['state'] = 1;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard($keyboard))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $message_text = 'Выберите время по Москве, когда Вам удобно получать уведомления';

                    $data['text'] = $message_text;
                    if ($text !== '') {
                        $data['text'] = $message_text;
                    }

                    $result = Request::sendMessage($data);
                    break;
                } else {
                    $search_id = array_search($text, $keyboard);
                    $id = $res[$search_id]['id'];
                }

                $notes['time'] = $id;
                $text = '';
            case 2:
                $res = $this->execApiFunc('getDays', [], $error);
                $keyboard = array_column($res, 'name');
//                $keyboard = ['1 день', '3 дня', '7 дней'];
                if ($text === '' || !in_array($text, $keyboard, true)) {
                    $notes['state'] = 2;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard($keyboard))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $message_text = 'Выберите за сколько дней до дня оплаты отправить Вам уведомление';

                    $data['text'] = $message_text;
                    if ($text !== '') {
                        $data['text'] = $message_text;
                    }

                    $result = Request::sendMessage($data);
                    break;
                } else {
                    $search_id = array_search($text, $keyboard);
                    $id = $res[$search_id]['id'];
                }

                $notes['days'] = $id;
                $text = '';
            case 3:
                $this->conversation->update();
                $params = [];

                $out_text = 'Отлично! Ваш домен успешно добавлен.' . PHP_EOL;
                unset($notes['state']);
                foreach ($notes as $k => $v) {
                    $params[$k] = $v;
                }

                $params['user_id'] = $user_id;
                $params['chat_id'] = $chat_id;
                $res = $this->execApiFunc('addDomain', $params, $error);

                $data['text'] = ($res ? $out_text : ('Ошибка!'.PHP_EOL.($error ?? 'Попробуйте позже.')));

                $this->conversation->stop();

                $result = Request::sendMessage($data);
                break;
        }

        return $result;
    }

    private function execApiFunc(string $method, array $params = [], string &$error = null)
    {
        $res = $this->Api->$method($params);
        if ($this->Api->getRequest()['status'] !== 200 || empty($res)) {
            $error = $this->Api->getMessage() ?? null;
            return false;
        }
        return $res;
    }
}