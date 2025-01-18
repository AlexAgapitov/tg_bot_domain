<?php

namespace Src\Infrastructure\Command;

use Src\Application\UseCase\GetTimes\GetTimesRequest;
use Src\Application\UseCase\GetTimes\GetTimesUseCase;

class GetTimesCommand
{
    private GetTimesUseCase $useCase;
    public function __construct(GetTimesUseCase $useCase)
    {
        $this->useCase = $useCase;
    }
    public function __invoke(GetTimesRequest $request)
    {
        $response = ($this->useCase)($request);
        return $response;
    }
}
