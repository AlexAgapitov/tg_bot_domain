<?php

namespace Domain\Commands;

use Core\Api;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class DeleteCommand extends UserCommand
{
    protected $name = 'delete';
    protected $description = 'Команда для удаления домена';
    protected $usage = '/delete';
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
        $message     = $this->getMessage();
        $command_str = trim($message->getText(true));
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

        $params['user_id'] = $user_id;
        $Api = new Api();
        $domains = $Api->getDomains($params);
        $message = null;

        if ($Api->getRequest()['status'] !== 200) {
            $data['text'] = "Ошибка! Команда в данный момент недоступна. Попробуйте позже.";
            $result = Request::sendMessage($data);
        } else if (empty($domains)) {
            $data['text'] = "У Вас не добавлено доменов. " . PHP_EOL . "Чтобы добавить домен введите /add";
            $result = Request::sendMessage($data);
        } else {
            switch ($state) {
                case 0:
                    $keyboard = array_column($domains, 'name');
                    if ($text === '' || !in_array($text, $keyboard, true)) {
                        $notes['state'] = 0;
                        $this->conversation->update();

                        $data['reply_markup'] = (new Keyboard($keyboard))
                            ->setResizeKeyboard(true)
                            ->setOneTimeKeyboard(true)
                            ->setSelective(true);

                        $message_text = 'Выберите домен, который вы хотите удалить';

                        $data['text'] = $message_text;
                        if ($text !== '') {
                            $data['text'] = $message_text;
                        }

                        $result = Request::sendMessage($data);
                        break;
                    } else {
                        $search_id = array_search($text, $keyboard);
                        $id = $domains[$search_id]['id'];
                    }

                    $notes['domain_id'] = $id;
                    $text = '';
                case 1:
                    $this->conversation->update();
                    $params = [];
                    unset($notes['state']);
                    foreach ($notes as $k => $v) {
                        $params[$k] = $v;
                    }

                    $params['user_id'] = $user_id;

                    $out_text = null;
                    $Api = new Api();
                    $res = $Api->deleteDomain($params);
                    if ($Api->getRequest()['status'] !== 200 || null === $res) {
                        $data['text'] = "Ошибка! Команда в данный момент недоступна. Попробуйте позже.";
                    } else {
                        $data['text'] = "Отлично! Ваш домен успешно удален.";
                    }

                    $this->conversation->stop();

                    $result = Request::sendMessage($data);
                    break;
            }
        }

        return $result;
    }
}