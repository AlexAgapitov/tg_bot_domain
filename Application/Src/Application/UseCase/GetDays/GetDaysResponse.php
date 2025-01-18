<?php

namespace Src\Application\UseCase\GetDays;

class GetDaysResponse
{
    public array $days;
    public function __construct(
        array $days
    )
    {
        $this->days = $days;
    }
}