<?php

namespace Src\Application\UseCase\GetTimes;

class GetTimesResponse
{
    public array $times;
    public function __construct(
        array $times
    )
    {
        $this->times = $times;
    }
}