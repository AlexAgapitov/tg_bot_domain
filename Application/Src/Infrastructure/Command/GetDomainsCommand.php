<?php

namespace Src\Infrastructure\Command;

use Src\Application\UseCase\GetDomains\GetDomainsRequest;
use Src\Application\UseCase\GetDomains\GetDomainsUseCase;

class GetDomainsCommand
{
    private GetDomainsUseCase $useCase;
    public function __construct(GetDomainsUseCase $useCase)
    {
        $this->useCase = $useCase;
    }
    public function __invoke(GetDomainsRequest $request)
    {
        $response = ($this->useCase)($request);
        return $response;
    }
}
