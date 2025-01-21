<?php

namespace Src\Application\UseCase\SubmitDomain;

class SubmitDomainRequest
{
    public int $user_id;
    public string $name;
    public int $time;
    public int $days;
    public function __construct(
        int $user_id,
        string $name,
        int $time,
        int $days
    )
    {
        $this->user_id = $user_id;
        $this->name = $name;
        $this->time = $time;
        $this->days = $days;
    }
}