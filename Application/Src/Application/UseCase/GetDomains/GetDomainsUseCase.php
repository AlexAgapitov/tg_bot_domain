<?php

namespace Src\Application\UseCase\GetDomains;

use Src\Domain\Factory\DomainFactoryInterface;
use Src\Domain\Repository\DomainRepositoryInterface;

class GetDomainsUseCase
{
    private DomainRepositoryInterface $repository;

    public function __construct(DomainRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(GetDomainsRequest $request): GetDomainsResponse
    {
        $answer = [];
        $domains = $this->repository->findByUserId($request->userId);

        foreach ($domains ?? [] AS $domain) {
            $answer[] = [
                'id' => $domain->getId(),
                'name' => $domain->getName()->getValue(),
            ];
        }

        return new GetDomainsResponse(
            $answer
        );
    }
}