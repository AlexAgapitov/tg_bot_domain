<?php

namespace Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Request;

class TestCommand extends UserCommand
{
    protected $name = 'test';                      // Your command's name
    protected $description = 'A command for test'; // Your command description
    protected $usage = '/test';                    // Usage of your command
    protected $version = '1.0.0';
    protected $need_mysql = false;                 // Version of your command

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

                    $data['text'] = 'Type your name:';

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['name'] = $text;
                $text          = '';

            // No break!
            case 1:
                if ($text === '') {
                    $notes['state'] = 1;
                    $this->conversation->update();

                    $data['text'] = 'Type your surname:';

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['surname'] = $text;
                $text             = '';

            // No break!
            case 2:
                if ($text === '' || !is_numeric($text)) {
                    $notes['state'] = 2;
                    $this->conversation->update();

                    $data['text'] = 'Type your age:';
                    if ($text !== '') {
                        $data['text'] = 'Age must be a number';
                    }

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['age'] = $text;
                $text         = '';

            // No break!
            case 3:
                if ($text === '' || !in_array($text, ['M', 'F'], true)) {
                    $notes['state'] = 3;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard(['M', 'F']))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = 'Select your gender:';
                    if ($text !== '') {
                        $data['text'] = 'Choose a keyboard option to select your gender';
                    }

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['gender'] = $text;

            // No break!
            case 4:
                if ($message->getLocation() === null) {
                    $notes['state'] = 4;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard(
                        (new KeyboardButton('Share Location'))->setRequestLocation(true)
                    ))
                        ->setOneTimeKeyboard(true)
                        ->setResizeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = 'Share your location:';

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['longitude'] = $message->getLocation()->getLongitude();
                $notes['latitude']  = $message->getLocation()->getLatitude();

            // No break!
            case 5:
                if ($message->getPhoto() === null) {
                    $notes['state'] = 5;
                    $this->conversation->update();

                    $data['text'] = 'Insert your picture:';

                    $result = Request::sendMessage($data);
                    break;
                }

                $photo             = $message->getPhoto()[0];
                $notes['photo_id'] = $photo->getFileId();

            // No break!
            case 6:
                if ($message->getContact() === null) {
                    $notes['state'] = 6;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard(
                        (new KeyboardButton('Share Contact'))->setRequestContact(true)
                    ))
                        ->setOneTimeKeyboard(true)
                        ->setResizeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = 'Share your contact information:';

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['phone_number'] = $message->getContact()->getPhoneNumber();

            // No break!
            case 7:
                $this->conversation->update();
                $out_text = '/Survey result:' . PHP_EOL;
                unset($notes['state']);
                foreach ($notes as $k => $v) {
                    $out_text .= PHP_EOL . ucfirst($k) . ': ' . $v;
                }

                $data['photo']   = $notes['photo_id'];
                $data['caption'] = $out_text;

                $this->conversation->stop();

                $result = Request::sendPhoto($data);
                break;
        }

        return $result;
    }
}