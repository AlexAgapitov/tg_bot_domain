<?php

namespace Src\Domain\Utils;

interface NotifyInterface
{
    public function payDate(int $user_id, string $domain, string $pay_date): void;
}