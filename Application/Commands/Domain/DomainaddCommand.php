<?php

namespace Domain\Commands;

use Core\Api;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;

class DomainaddCommand extends UserCommand
{
    protected $name = 'domainadd';
    protected $description = 'A command for add domain';
    protected $usage = '/domainadd';
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
                $Api = new Api();
                $res = $Api->getTimes();
                if ($Api->getRequest()['status'] !== 200 || empty($res)) {

                }
                $keyboard = array_column($res, 'name');
//                $keyboard = ['11:00 - 12:00', '12:00 - 13:00'];
                if ($text === '' || !in_array($text, $keyboard, true)) {
                    $notes['state'] = 1;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard($keyboard))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = 'Выберите время по Москве, когда Вам удобно получать уведомления'.json_encode($res);
                    if ($text !== '') {
                        $data['text'] = 'Выберите время по Москве, когда Вам удобно получать уведомления'.json_encode($res);
                    }

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['time'] = $text;
                $text = '';
            case 2:
                $Api = new Api();
                $res = $Api->getDays();
                if ($Api->getRequest()['status'] !== 200 || empty($res)) {

                }
                $keyboard = array_column($res, 'name');
//                $keyboard = ['1 день', '3 дня', '7 дней'];
                if ($text === '' || !in_array($text, $keyboard, true)) {
                    $notes['state'] = 2;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard($keyboard))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = 'Выберите за сколько дней до дня оплаты отправить Вам уведомление';
                    if ($text !== '') {
                        $data['text'] = 'Выберите за сколько дней до дня оплаты отправить Вам уведомление';
                    }

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['days'] = $text;
                $text = '';
            case 3:
                $this->conversation->update();
                $out_text = '/Survey result:' . PHP_EOL;
                unset($notes['state']);
                foreach ($notes as $k => $v) {
                    $out_text .= PHP_EOL . ucfirst($k) . ': ' . $v;
                }
                $data['text'] = $out_text;

                $this->conversation->stop();

                $result = Request::sendMessage($data);
                break;
        }

        return $result;
    }
}