<?php

namespace Core;

class Router
{
    public static array $params = [];

    private static string $error_message;
    private static int $error_code;

    public static function addDomain(array $params)
    {
        try {
//            $params = self::$params['POST'];

            $url = parse_url($params['name']);

            $params['name'] = $url['host'] ?? $url['path'];

            if (empty($params['name'])) {
                throw new \Exception('Error format domain');
            }

            $Repository = new \Src\Infrastructure\Repository\DomainRepository();
            $Factory = new \Src\Infrastructure\Factory\CommonDomainFactory();
            $PayDate = new \Src\Infrastructure\Utils\Whois();
            $UseCase = new \Src\Application\UseCase\SubmitDomain\SubmitDomainUseCase($Factory, $Repository, $PayDate);
            $Command = new \Src\Infrastructure\Command\SubmitDomainCommand($UseCase);
            $Request = new \Src\Application\UseCase\SubmitDomain\SubmitDomainRequest($params['user_id'], $params['name'], $params['time'], $params['days']);
            $result = $Command($Request);

            return $result;
        } catch (\Exception $e) {
            self::$error_message = $e->getMessage();
            return false;
        }
    }

    public static function checkPayDate()
    {
        try {
            $Repository = new \Src\Infrastructure\Repository\DomainRepository();
            $PayDate = new \Src\Infrastructure\Utils\Whois();
            $Notify = new \Src\Infrastructure\Utils\TgNotify();
            $UseCase = new \Src\Application\UseCase\CheckPayDate\CheckPayDateUseCase($Repository, $PayDate, $Notify);
            $Command = new \Src\Infrastructure\Command\CheckPayDateCommand($UseCase);
            $Request = new \Src\Application\UseCase\CheckPayDate\CheckPayDateRequest();
            $result = $Command($Request);

            return $result->pay_date;
        } catch (\Exception $e) {
            self::$error_message = $e->getMessage();
            return false;
        }
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

    public static function getDomains($params): array
    {
//        $params = self::$params['POST'];
        $Repository = new \Src\Infrastructure\Repository\DomainRepository();
        $UseCase = new \Src\Application\UseCase\GetDomains\GetDomainsUseCase($Repository);
        $Command = new \Src\Infrastructure\Command\GetDomainsCommand($UseCase);
        $Request = new \Src\Application\UseCase\GetDomains\GetDomainsRequest($params['user_id']);
        $result = $Command($Request);

        return $result->domains;
    }

    public static function getErrorMessage(): ?string
    {
        return self::$error_message ?? '';
    }

//    public static function setParams()
//    {
//        self::$params = [
//            'GET' => $_GET,
//            'POST' => json_decode(file_get_contents('php://input'), true) ?? [],
////            'POST' => $_POST,
//        ];
//    }
}