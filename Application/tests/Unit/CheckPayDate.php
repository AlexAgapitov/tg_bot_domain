<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../autoload.php';

class CheckPayDate extends TestCase
{
    public function testExceptionUpdate()
    {
        $domainMock = $this->getMockBuilder(\Src\Domain\Entity\Domain::class)
            ->onlyMethods(['getName', 'getPayDate', 'getUserId'])
            ->disableOriginalConstructor()
            ->getMock();

        $RepositoryMock = $this->getMockBuilder(\Src\Infrastructure\Repository\DomainRepository::class)
            ->onlyMethods(['updateForCheck', 'findForCheck'])
            ->getMock();

        $RepositoryMock->method('findForCheck')
            ->willReturn($domainMock);

        $RepositoryMock->method('updateForCheck')
            ->willThrowException(new \Exception('Error update domain'));

        $PayDateMock = $this->getMockBuilder(\Src\Infrastructure\Utils\Whois::class)
            ->onlyMethods(['getPayDate'])
            ->getMock();

        $PayDateMock->method('getPayDate')
            ->WillReturn(null);

        $NotifyMock = $this->getMockBuilder(\Src\Infrastructure\Utils\TgNotify::class)
            ->onlyMethods(['payDate'])
            ->getMock();

        $UseCase = new \Src\Application\UseCase\CheckPayDate\CheckPayDateUseCase($RepositoryMock, $PayDateMock, $NotifyMock);
        $Command = new \Src\Infrastructure\Command\CheckPayDateCommand($UseCase);
        $Request = new \Src\Application\UseCase\CheckPayDate\CheckPayDateRequest();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error update domain');
        $Command($Request);
    }
}
