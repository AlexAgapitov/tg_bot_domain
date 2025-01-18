<?php

namespace Core;

use Exception;

class App
{
    public function run()
    {
        $args = $_SERVER['argv'];

        switch ($args[1]) {
            case 'add':
                $this->add($args[2]);
                break;
            case 'getTimes':
                $this->getTimes();
                break;
            case 'getDays':
                $this->getDays();
                break;
            default:
                throw new Exception('Command not found');
        }
    }

    private function add(string $json = null)
    {
        $array = !empty($json) ? json_decode($json, true) : null;
        if (empty($array)) {
            throw new Exception('Error in string');
        }

        $Repository = new \Src\Infrastructure\Repository\DomainRepository();
        $Factory = new \Src\Infrastructure\Factory\CommonDomainFactory();
        $UseCase = new \Src\Application\UseCase\SubmitDomain\SubmitDomainUseCase($Factory, $Repository);
        $Command = new \Src\Infrastructure\Command\SubmitDomainCommand($UseCase);
        $Request = new \Src\Application\UseCase\SubmitDomain\SubmitDomainRequest($array['user_id'], $array['name'], $array['time'], $array['days']);
        $result = $Command($Request);

        var_dump("id in storage: ".$result->id);
    }

    private function getTimes()
    {
        $Repository = new \Src\Infrastructure\Repository\TimesRepository();
        $Redis = new \Src\Infrastructure\Gateway\RedisGateway();
        $UseCase = new \Src\Application\UseCase\GetTimes\GetTimesUseCase($Repository, $Redis);
        $Command = new \Src\Infrastructure\Command\GetTimesCommand($UseCase);
        $Request = new \Src\Application\UseCase\GetTimes\GetTimesRequest();
        $result = $Command($Request);

        var_dump($result);
    }

    private function getDays()
    {
        $Repository = new \Src\Infrastructure\Repository\DaysRepository();
        $Redis = new \Src\Infrastructure\Gateway\RedisGateway();
        $UseCase = new \Src\Application\UseCase\GetDays\GetDaysUseCase($Repository, $Redis);
        $Command = new \Src\Infrastructure\Command\GetDaysCommand($UseCase);
        $Request = new \Src\Application\UseCase\GetDays\GetDaysRequest();
        $result = $Command($Request);

        var_dump($result);
    }

}