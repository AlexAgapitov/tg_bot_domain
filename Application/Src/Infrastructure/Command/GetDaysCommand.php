<?php

namespace Src\Infrastructure\Command;

use Src\Application\UseCase\GetDays\GetDaysRequest;
use Src\Application\UseCase\GetDays\GetDaysUseCase;

class GetDaysCommand
{
    private GetDaysUseCase $useCase;
    public function __construct(GetDaysUseCase $useCase)
    {
        $this->useCase = $useCase;
    }
    public function __invoke(GetDaysRequest $request)
    {
        $response = ($this->useCase)($request);
        return $response;
    }
}
