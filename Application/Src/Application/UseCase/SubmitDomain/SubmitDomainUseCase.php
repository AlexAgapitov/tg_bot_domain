<?php

namespace Src\Application\UseCase\SubmitDomain;

use Src\Domain\Factory\DomainFactoryInterface;
use Src\Domain\Repository\DomainRepositoryInterface;

class SubmitDomainUseCase
{
    private DomainRepositoryInterface $repository;
    private DomainFactoryInterface $factory;

    public function __construct(DomainFactoryInterface $factory, DomainRepositoryInterface $repository)
    {
        $this->factory = $factory;
        $this->repository = $repository;
    }

    public function __invoke(SubmitDomainRequest $request): SubmitDomainResponse
    {
        $domain = $this->factory->create($request->userId, $request->name, $request->days, $request->time);

        $this->repository->save($domain);

        return new SubmitDomainResponse(
            $domain->getId()
        );
    }
}