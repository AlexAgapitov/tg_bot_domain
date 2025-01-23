<?php

namespace Src\Application\UseCase\DeleteDomain;

use Src\Domain\Repository\DomainRepositoryInterface;

class DeleteDomainUseCase
{
    private DomainRepositoryInterface $repository;

    public function __construct(DomainRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(DeleteDomainRequest $request): DeleteDomainResponse
    {
        $domain = $this->repository->findById($request->domain_id);

        if (is_null($domain)) {
            throw new \Exception('Domain not found');
        }

        if ($domain->getUserId()->getValue() !== $request->user_id) {
            throw new \Exception('Domain does not belong to you');
        }

        $this->repository->delete($domain->getId());

        return new DeleteDomainResponse();
    }
}