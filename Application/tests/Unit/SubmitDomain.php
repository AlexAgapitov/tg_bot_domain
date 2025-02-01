<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../autoload.php';

class SubmitDomain extends TestCase
{
    public function testExceptionPayDateNotFound()
    {
        $params = ['name' => 'test_test', 'user_id' => 1, 'time' => 1, 'days' => 1];
        $Repository = new \Src\Infrastructure\Repository\DomainRepository();
        $Factory = new \Src\Infrastructure\Factory\CommonDomainFactory();


        $PayDateMock = $this->helper_PayDateMock();

        $PayDateMock->method('getPayDate')
            ->WillReturn(null);

        $UseCase = new \Src\Application\UseCase\SubmitDomain\SubmitDomainUseCase($Factory, $Repository, $PayDateMock);
        $Command = new \Src\Infrastructure\Command\SubmitDomainCommand($UseCase);
        $Request = new \Src\Application\UseCase\SubmitDomain\SubmitDomainRequest($params['user_id'], $params['name'], $params['time'], $params['days']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Pay date not found');
        $Command($Request);
    }

    public function testExceptionInvalidName()
    {
        $params = ['name' => '', 'user_id' => 1, 'time' => 1, 'days' => 1];
        $Repository = new \Src\Infrastructure\Repository\DomainRepository();
        $Factory = new \Src\Infrastructure\Factory\CommonDomainFactory();


        $PayDateMock = $this->helper_PayDateMock();

        $PayDateMock->method('getPayDate')
            ->WillReturn('2026-01-01');

        $UseCase = new \Src\Application\UseCase\SubmitDomain\SubmitDomainUseCase($Factory, $Repository, $PayDateMock);
        $Command = new \Src\Infrastructure\Command\SubmitDomainCommand($UseCase);
        $Request = new \Src\Application\UseCase\SubmitDomain\SubmitDomainRequest($params['user_id'], $params['name'], $params['time'], $params['days']);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Name');
        $Command($Request);
    }

    public function testExceptionInvalidTime()
    {
        $params = ['name' => 't', 'user_id' => 1, 'time' => 0, 'days' => 1];
        $Repository = new \Src\Infrastructure\Repository\DomainRepository();
        $Factory = new \Src\Infrastructure\Factory\CommonDomainFactory();

        $PayDateMock = $this->helper_PayDateMock();

        $PayDateMock->method('getPayDate')
            ->WillReturn('2026-01-01');

        $UseCase = new \Src\Application\UseCase\SubmitDomain\SubmitDomainUseCase($Factory, $Repository, $PayDateMock);
        $Command = new \Src\Infrastructure\Command\SubmitDomainCommand($UseCase);
        $Request = new \Src\Application\UseCase\SubmitDomain\SubmitDomainRequest($params['user_id'], $params['name'], $params['time'], $params['days']);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Time');
        $Command($Request);
    }

    public function testExceptionInvalidDays()
    {
        $params = ['name' => 't', 'user_id' => 1, 'time' => 1, 'days' => 0];
        $Repository = new \Src\Infrastructure\Repository\DomainRepository();
        $Factory = new \Src\Infrastructure\Factory\CommonDomainFactory();

        $PayDateMock = $this->helper_PayDateMock();

        $PayDateMock->method('getPayDate')
            ->WillReturn('2026-01-01');

        $UseCase = new \Src\Application\UseCase\SubmitDomain\SubmitDomainUseCase($Factory, $Repository, $PayDateMock);
        $Command = new \Src\Infrastructure\Command\SubmitDomainCommand($UseCase);
        $Request = new \Src\Application\UseCase\SubmitDomain\SubmitDomainRequest($params['user_id'], $params['name'], $params['time'], $params['days']);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Days');
        $Command($Request);
    }

    public function testExceptionDomainExist()
    {
        $params = ['name' => 't', 'user_id' => 1, 'time' => 1, 'days' => 1];
        $Factory = new \Src\Infrastructure\Factory\CommonDomainFactory();

        $RepositoryMock = $this->helper_RepositoryMock();

        $RepositoryMock->method('save')
            ->willThrowException(new \Exception('Domain exist'));

        $PayDateMock = $this->helper_PayDateMock();

        $PayDateMock->method('getPayDate')
            ->WillReturn('2026-01-01');

        $UseCase = new \Src\Application\UseCase\SubmitDomain\SubmitDomainUseCase($Factory, $RepositoryMock, $PayDateMock);
        $Command = new \Src\Infrastructure\Command\SubmitDomainCommand($UseCase);
        $Request = new \Src\Application\UseCase\SubmitDomain\SubmitDomainRequest($params['user_id'], $params['name'], $params['time'], $params['days']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Domain exist');
        $Command($Request);
    }

    public function testExceptionErrorSave()
    {
        $params = ['name' => 't', 'user_id' => 1, 'time' => 1, 'days' => 1];
        $Factory = new \Src\Infrastructure\Factory\CommonDomainFactory();

        $RepositoryMock = $this->helper_RepositoryMock();

        $RepositoryMock->method('save')
            ->willThrowException(new \Exception('Error save domain'));

        $PayDateMock = $this->helper_PayDateMock();

        $PayDateMock->method('getPayDate')
            ->WillReturn('2026-01-01');

        $UseCase = new \Src\Application\UseCase\SubmitDomain\SubmitDomainUseCase($Factory, $RepositoryMock, $PayDateMock);
        $Command = new \Src\Infrastructure\Command\SubmitDomainCommand($UseCase);
        $Request = new \Src\Application\UseCase\SubmitDomain\SubmitDomainRequest($params['user_id'], $params['name'], $params['time'], $params['days']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error save domain');
        $Command($Request);
    }
    
    private function helper_PayDateMock()
    {
        return $this->getMockBuilder(\Src\Infrastructure\Utils\Whois::class)
            ->onlyMethods(['getPayDate'])
            ->getMock();
    }
    
    private function helper_RepositoryMock()
    {
        return $this->getMockBuilder(\Src\Infrastructure\Repository\DomainRepository::class)
            ->onlyMethods(['save'])
            ->getMock();
    }
}