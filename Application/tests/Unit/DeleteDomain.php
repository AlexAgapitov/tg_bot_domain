<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../autoload.php';

class DeleteDomain extends TestCase
{
    public function testExceptionNotFound()
    {
        $params = ['domain_id' => 1, 'user_id' => 1];
        $RepositoryMock = $this->getMockBuilder(\Src\Infrastructure\Repository\DomainRepository::class)
            ->onlyMethods(['findById'])
            ->getMock();

        $RepositoryMock->method('findById')
            ->willReturn(null);

        $UseCase = new \Src\Application\UseCase\DeleteDomain\DeleteDomainUseCase($RepositoryMock);
        $Command = new \Src\Infrastructure\Command\DeleteDomainCommand($UseCase);
        $Request = new \Src\Application\UseCase\DeleteDomain\DeleteDomainRequest($params['user_id'], $params['domain_id']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Domain not found');
        $Command($Request);
    }

    public function testExceptionNotBelong()
    {
        $params = ['domain_id' => 1, 'user_id' => 1];

        $domainMock = $this->getMockBuilder(\Src\Domain\Entity\Domain::class)
            ->onlyMethods(['getUserId'])
            ->disableOriginalConstructor()
            ->getMock();

        $userIdMock = $this->getMockBuilder(\Src\Domain\ValueObject\UserId::class)
            ->onlyMethods(['getValue'])
            ->disableOriginalConstructor()
            ->getMock();

        $userIdMock->method('getValue')
            ->willReturn(2);

        $domainMock->method('getUserId')
            ->willReturn($userIdMock);

        $RepositoryMock = $this->getMockBuilder(\Src\Infrastructure\Repository\DomainRepository::class)
            ->onlyMethods(['findById'])
            ->getMock();

        $RepositoryMock->method('findById')
            ->willReturn($domainMock);

        $UseCase = new \Src\Application\UseCase\DeleteDomain\DeleteDomainUseCase($RepositoryMock);
        $Command = new \Src\Infrastructure\Command\DeleteDomainCommand($UseCase);
        $Request = new \Src\Application\UseCase\DeleteDomain\DeleteDomainRequest($params['user_id'], $params['domain_id']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Domain does not belong to you');
        $Command($Request);
    }

    public function testExceptionDelete()
    {
        $params = ['domain_id' => 1, 'user_id' => 1];

        $domainMock = $this->getMockBuilder(\Src\Domain\Entity\Domain::class)
            ->onlyMethods(['getUserId', 'getId'])
            ->disableOriginalConstructor()
            ->getMock();

        $userIdMock = $this->getMockBuilder(\Src\Domain\ValueObject\UserId::class)
            ->onlyMethods(['getValue'])
            ->disableOriginalConstructor()
            ->getMock();

        $userIdMock->method('getValue')
            ->willReturn(1);

        $domainMock->method('getId')
            ->willReturn(1);

        $domainMock->method('getUserId')
            ->willReturn($userIdMock);

        $RepositoryMock = $this->getMockBuilder(\Src\Infrastructure\Repository\DomainRepository::class)
            ->onlyMethods(['findById', 'delete'])
            ->getMock();

        $RepositoryMock->method('findById')
            ->willReturn($domainMock);

        $RepositoryMock->method('delete')
            ->willThrowException(new \Exception('Error delete domain'));

        $UseCase = new \Src\Application\UseCase\DeleteDomain\DeleteDomainUseCase($RepositoryMock);
        $Command = new \Src\Infrastructure\Command\DeleteDomainCommand($UseCase);
        $Request = new \Src\Application\UseCase\DeleteDomain\DeleteDomainRequest($params['user_id'], $params['domain_id']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error delete domain');
        $Command($Request);
    }
}
