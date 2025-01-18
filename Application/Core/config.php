<?php
return [
    'bot_api_key' => '8083746843:AAE10syeqtx0H45K9dlsgClHiktolAZmoTo',
    'bot_username' => 'dont_forget_pay_domen_bot',
    'bot_hook_url' => 'https://aga-tg-bots.ru/Service/hook.php',

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