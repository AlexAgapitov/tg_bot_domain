<?php

namespace Src\Infrastructure\Utils;

use Src\Domain\Utils\NotifyInterface;

class TgNotify implements NotifyInterface
{

    public function payDate(int $user_id, string $domain, string $pay_date): void
    {
        $message = "Дата оплаты домена {$domain} уже близко! Домен оплачен до " . $pay_date;
        \Core\TgBot::sendMessage($user_id, $message);
    }
}