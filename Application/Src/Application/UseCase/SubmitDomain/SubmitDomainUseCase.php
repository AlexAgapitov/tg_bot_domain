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
        $pay_date = $this->payDate->getPayDate($request->name);

        if (empty($pay_date)) {
            throw new \Exception('Pay date not found');
        }

        $domain = $this->factory->create($request->user_id, $request->name, $request->days, $request->time, $pay_date);

        $this->repository->save($domain);

        return new SubmitDomainResponse(
            $domain->getId(),
            $domain->getName()->getValue(),
            $pay_date
        );
    }
}