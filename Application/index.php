<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$data = json_decode(file_get_contents('php://input'), true);
file_put_contents('file.txt', '$data: '.print_r($data, 1)."\n", FILE_APPEND);
file_put_contents('file.txt', '$data: '.print_r($_REQUEST, 1)."\n", FILE_APPEND);

$data = !empty($data['callback_query']) ? $data['callback_query'] : $data['message'];

$message = mb_strtolower((!empty($data['text']) ? $data['text'] : $data['data']), 'utf-8');

switch ($message) {
    case '/start':
        $method = 'sendMessage';
        $send_data = [
            'text' => "Привет, я умею отправлять уведомления об оплате домена!\nВыбери кнопку добавить домен, чтобы мной воспользоваться!",
            'reply_markup' => [
                'resize_keyboard' => true,
                'keyboard' => [
                    [
                        ['text' => 'Добавить домен'],
                    ],
                ]
            ]
        ];
        break;
    case 'Добавить домен':
        $method = 'sendMessage';
        $send_data = [
            'text' => 'Введите адрес вашего домена.',
        ];
//        $send_data = [
//            'text' => 'Выберите за какое количество дней Вам отправить уведомление!',
//            'reply_markup' => [
//                'resize_keyboard' => true,
//                'keyboard' => [
//                    [
//                        ['text' => 'Добавить домен'],
//                        ['text' => 'Добавить домен'],
//                        ['text' => 'Добавить домен'],
//                    ],
//                ]
//            ]
//        ];
        break;
    default:
        $method = 'sendMessage';
        $send_data = [
            'text' => 'Неизвестная команда'
        ];
        break;
}

$send_data['chat_id'] = $data['chat']['id'];

$res = sendTelegram($method, $send_data);

function sendTelegram($method, $send_data, $headers = []) {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.telegram.org/bot'.'8083746843:AAE10syeqtx0H45K9dlsgClHiktolAZmoTo'.'/'.$method,
        CURLOPT_POSTFIELDS => json_encode($send_data),
        CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"), $headers)
    ]);
    $result = curl_exec($curl);
    curl_close($curl);
    return (json_encode($result, true) ? json_decode($result, true) : $result);
}


// https://api.telegram.org/bot8083746843:AAE10syeqtx0H45K9dlsgClHiktolAZmoTo/setWebhook?https://aga-tourist.ru/telegram.php