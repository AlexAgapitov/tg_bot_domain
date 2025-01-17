<?php

namespace Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Keyboard;
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
        $message = $this->getMessage();            // Get Message object

        $chat_id = $message->getChat()->getId();   // Get the current Chat ID

        $data = [                                  // Set up the new message data
            'chat_id' => $chat_id,                 // Set Chat ID to send the message to
            'text'    => 'This is just a Test...', // Set message to send
        ];
//
//        // Digits with operations
//        $keyboards[] = new Keyboard(
//            ['7', '8', '9', '+'],
//            ['4', '5', '6', '-'],
//            ['1', '2', '3', '*'],
//            [' ', '0', ' ', '/']
//        );
//
//        $data['reply_markup'] = $keyboards;

        return Request::sendMessage($data);        // Send message!
    }
}