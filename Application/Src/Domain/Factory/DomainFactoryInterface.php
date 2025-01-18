<?php

namespace Src\Domain\Factory;

use Src\Domain\Entity\Domain;

interface DomainFactoryInterface
{
    public function create(int $userId, string $name, int $days, int $time): Domain;
}