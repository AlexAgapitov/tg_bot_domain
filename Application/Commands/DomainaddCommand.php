<?php

namespace Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
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
        $chat    = $message->getChat();
        $user    = $message->getFrom();
        $text    = trim($message->getText(true));
        $chat_id = $chat->getId();
        $user_id = $user->getId();

        // Preparing response
        $data = [
            'chat_id'      => $chat_id,
            // Remove any keyboard by default
            'reply_markup' => Keyboard::remove(['selective' => true]),
        ];

        // Conversation start
        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

        // Load any existing notes from this conversation
        $notes = &$this->conversation->notes;
        !is_array($notes) && $notes = [];

        // Load the current state of the conversation
        $state = $notes['state'] ?? 0;

        $result = Request::emptyResponse();

        // State machine
        // Every time a step is achieved the state is updated
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
                $text          = '';
            case 1:
                $keyboard = ['11:00 - 12:00', '12:00 - 13:00'];
                if ($text === '' || !in_array($text, $keyboard, true)) {
                    $notes['state'] = 3;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard($keyboard))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = 'Выберите время по Москве, когда Вам удобно получать уведомления';
                    if ($text !== '') {
                        $data['text'] = 'Выберите время по Москве, когда Вам удобно получать уведомления';
                    }

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['time'] = $text;
                $text         = '';
            case 2:
                $keyboard = ['1 день', '3 дня', '7 дней'];
                if ($text === '' || !in_array($text, $keyboard, true)) {
                    $notes['state'] = 3;
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

                $notes['time'] = $text;
                $text         = '';
            case 3:
                $this->conversation->update();
                $out_text = '/Survey result:' . PHP_EOL;
                unset($notes['state']);
                foreach ($notes as $k => $v) {
                    $out_text .= PHP_EOL . ucfirst($k) . ': ' . $v;
                }
                $data['text'] = $out_text;

                $this->conversation->stop();

                $result = Request::sendMediaGroup($data);
                break;
        }

        return $result;
    }
}