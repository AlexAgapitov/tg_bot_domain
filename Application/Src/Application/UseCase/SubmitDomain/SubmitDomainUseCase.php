<?php

namespace Src\Application\UseCase\SubmitDomain;

use Src\Domain\Factory\DomainFactoryInterface;
use Src\Domain\Utils\PayDateInterface;
use Src\Domain\Repository\DomainRepositoryInterface;

class SubmitDomainUseCase
{
    private DomainRepositoryInterface $repository;
    private DomainFactoryInterface $factory;
    private PayDateInterface $payDate;

    public function __construct(DomainFactoryInterface $factory, DomainRepositoryInterface $repository, PayDateInterface $payDate)
    {
        $this->factory = $factory;
        $this->repository = $repository;
        $this->payDate = $payDate;
    }

    public function __invoke(SubmitDomainRequest $request): SubmitDomainResponse
    {
        $payDate = $this->payDate->getPayDate($request->name);

        $domain = $this->factory->create($request->userId, $request->name, $request->days, $request->time, $payDate);

        $this->repository->save($domain);

        return new SubmitDomainResponse(
            $domain->getId(),
            $domain->getName()->getValue(),
            $payDate->format('Y-m-d H:i:s')
        );
    }
}