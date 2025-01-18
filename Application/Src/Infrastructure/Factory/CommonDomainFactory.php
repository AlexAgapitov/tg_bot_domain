<?php

namespace Src\Infrastructure\Factory;

use Src\Domain\Factory\DomainFactoryInterface;
use Src\Domain\Entity\Domain;
use Src\Domain\ValueObject\UserId;
use Src\Domain\ValueObject\Name;
use Src\Domain\ValueObject\Days;
use Src\Domain\ValueObject\Time;

class CommonDomainFactory implements DomainFactoryInterface
{
    public function create(int $userId, string $name, int $days, int $time): Domain
    {
        return new Domain(
            new UserId($userId),
            new Name($name),
            new Time($time),
            new Days($days)
        );
    }
}