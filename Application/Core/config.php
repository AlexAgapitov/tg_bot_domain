<?php
return [
    'bot_api_key' => $_ENV['bot_api_key'],
    'bot_username' => $_ENV['bot_username'],
    'bot_hook_url' => $_ENV['domain'].'Service/hook.php',

    'commands' => [
        'paths' => [
             __DIR__ . '/../Commands',
        ]
    ],

     'mysql' => [
         'host'     => $_ENV['db_host'],
         'port'     => 3306, // optional
         'user'     => $_ENV['db_user'],
         'password' => $_ENV['db_pass'],
         'database' => $_ENV['db_name_tg'],
     ],
];