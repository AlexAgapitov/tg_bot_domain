<?php

namespace Domain\Commands;

use Core\Api;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;

class AddCommand extends UserCommand
{
    protected $name = 'add';
    protected $description = 'A command for add domain';
    protected $usage = '/add';
    protected $version = '1.0.0';
    protected $need_mysql = false;

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
                $keyboard = array_column($this->getTimes(), 'name');
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
                }

                $notes['time'] = $text;
                $text = '';
            case 2:
                $keyboard = array_column($this->getDays(), 'name');
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
                }

                $notes['days'] = $text;
                $text = '';
            case 3:
                $this->conversation->update();
                $keys = ['name', 'time', 'days'];
                $params = [];

                $out_text = 'Отлично! Ваш домен добавлен.' . PHP_EOL;
                unset($notes['state']);
                foreach ($notes as $k => $v) {
                    $params[$k] = $v;
                    $out_text .= PHP_EOL . ucfirst($k) . ': ' . $v;
                }

                $params['user_id'] = $chat_id;
                $params['time'] = 1;
                $params['days'] = 2;
                $res = $this->addDomain($params);

                $data['text'] = ($res ? $out_text : 'Ошибка! Попробуйте позже.');

                $this->conversation->stop();

                $result = Request::sendMessage($data);
                break;
        }

        return $result;
    }

    private function getTimes()
    {
        $Api = new Api();
        $res = $Api->getTimes();
        if ($Api->getRequest()['status'] !== 200 || empty($res)) {

        }
        return $res;
    }

    private function getDays()
    {
        $Api = new Api();
        $res = $Api->getDays();
        if ($Api->getRequest()['status'] !== 200 || empty($res)) {

        }
        return $res;
    }

    private function addDomain(array $res)
    {
        $Api = new Api();
        $res = $Api->addDomain($res);
        if ($Api->getRequest()['status'] !== 200 || empty($res)) {
            return false;
        }
        return true;
    }
}