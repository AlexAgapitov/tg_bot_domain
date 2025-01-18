<?php

namespace Src\Application\Gateway;

interface RedisGatewayInterface
{
    public function getTimes();
    public function setTimes(array $times);
    public function getDays();
    public function setDays(array $days);
}