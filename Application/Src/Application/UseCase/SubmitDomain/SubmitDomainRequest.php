<?php

namespace Src\Application\UseCase\SubmitDomain;

class SubmitDomainRequest
{
    public int $userId;
    public string $name;
    public int $time;
    public int $days;
    public function __construct(
        int $userId,
        string $name,
        int $time,
        int $days
    )
    {
        $this->userId = $userId;
        $this->name = $name;
        $this->time = $time;
        $this->days = $days;
    }
}