<?php

namespace Src\Infrastructure\Command;

use Src\Application\UseCase\SubmitDomain\SubmitDomainRequest;
use Src\Application\UseCase\SubmitDomain\SubmitDomainUseCase;

class SubmitDomainCommand
{
    private SubmitDomainUseCase $useCase;
    public function __construct(SubmitDomainUseCase $useCase)
    {
        $this->useCase = $useCase;
    }
    public function __invoke(SubmitDomainRequest $request)
    {
        $response = ($this->useCase)($request);
        return $response;
    }
}
