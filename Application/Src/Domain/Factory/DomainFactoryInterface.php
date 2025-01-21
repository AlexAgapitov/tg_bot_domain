<?php

namespace Src\Domain\Factory;

use Src\Domain\Entity\Domain;

interface DomainFactoryInterface
{
    public function create(int $user_id, string $name, int $days, int $time, string $pay_date): Domain;
}