<?php

namespace Src\Infrastructure\Gateway;

use Src\Application\Gateway\RedisGatewayInterface;

class RedisGateway implements RedisGatewayInterface
{
    private static \Predis\Client $Client;

    private string $times_key = 'times_dictionary';
    private string $days_key = 'days_dictionary';

    public function __construct()
    {
        self::$Client = new \Predis\Client(['host' => $_ENV['redis_host'], 'port' => $_ENV['redis_port']]);
    }

    public function getTimes()
    {
        $times = self::$Client->executeRaw(["GET", $this->times_key], $error);
        return !empty($times) ? json_decode($times, true) : null;
    }

    public function setTimes(array $times)
    {
        self::$Client->executeRaw(["SET", $this->times_key, json_encode($times)], $error);
    }

    public function getDays()
    {
        $days = self::$Client->executeRaw(["GET", $this->days_key], $error);
        return !empty($days) ? json_decode($days, true) : null;
    }

    public function setDays(array $days)
    {
        self::$Client->executeRaw(["SET", $this->days_key, json_encode($days)], $error);
    }
}