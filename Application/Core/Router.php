<?php

namespace Core;

class Router
{

    public static function addDomain()
    {
        $Repository = new \Src\Infrastructure\Repository\DomainRepository();
        $Factory = new \Src\Infrastructure\Factory\CommonDomainFactory();
        $UseCase = new \Src\Application\UseCase\SubmitDomain\SubmitDomainUseCase($Factory, $Repository);
        $Command = new \Src\Infrastructure\Command\SubmitDomainCommand($UseCase);
        $Request = new \Src\Application\UseCase\SubmitDomain\SubmitDomainRequest(self::$params['user_id'], self::$params['name'], self::$params['time'], self::$params['days']);
        $result = $Command($Request);

        return $result->id;
    }

    public static function getTimes(): array
    {
        $Repository = new \Src\Infrastructure\Repository\TimesRepository();
        $Redis = new \Src\Infrastructure\Gateway\RedisGateway();
        $UseCase = new \Src\Application\UseCase\GetTimes\GetTimesUseCase($Repository, $Redis);
        $Command = new \Src\Infrastructure\Command\GetTimesCommand($UseCase);
        $Request = new \Src\Application\UseCase\GetTimes\GetTimesRequest();
        $result = $Command($Request);

        return $result->times;
    }

    public static function getDays(): array
    {
        $Repository = new \Src\Infrastructure\Repository\DaysRepository();
        $Redis = new \Src\Infrastructure\Gateway\RedisGateway();
        $UseCase = new \Src\Application\UseCase\GetDays\GetDaysUseCase($Repository, $Redis);
        $Command = new \Src\Infrastructure\Command\GetDaysCommand($UseCase);
        $Request = new \Src\Application\UseCase\GetDays\GetDaysRequest();
        $result = $Command($Request);

        return $result->days;
    }
}