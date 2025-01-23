<?php

namespace Src\Infrastructure\Command;

use Src\Application\UseCase\DeleteDomain\DeleteDomainRequest;
use Src\Application\UseCase\DeleteDomain\DeleteDomainUseCase;

class DeleteDomainCommand
{
    private DeleteDomainUseCase $useCase;
    public function __construct(DeleteDomainUseCase $useCase)
    {
        $this->useCase = $useCase;
    }
    public function __invoke(DeleteDomainRequest $request)
    {
        $response = ($this->useCase)($request);
        return $response;
    }
}
