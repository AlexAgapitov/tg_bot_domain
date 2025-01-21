<?php

namespace Src\Infrastructure\Factory;

use Src\Domain\Factory\DomainFactoryInterface;
use Src\Domain\Entity\Domain;
use Src\Domain\ValueObject\PayDate;
use Src\Domain\ValueObject\UserId;
use Src\Domain\ValueObject\Name;
use Src\Domain\ValueObject\Days;
use Src\Domain\ValueObject\Time;

class CommonDomainFactory implements DomainFactoryInterface
{
    public function create(int $user_id, string $name, int $days, int $time, string $pay_date): Domain
    {
        return new Domain(
            new UserId($user_id),
            new Name($name),
            new Time($time),
            new Days($days),
            new PayDate($pay_date)
        );
    }
}