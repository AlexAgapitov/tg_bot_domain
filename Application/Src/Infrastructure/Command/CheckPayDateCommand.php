<?php

namespace Src\Infrastructure\Command;

use Src\Application\UseCase\CheckPayDate\CheckPayDateUseCase;
use Src\Application\UseCase\CheckPayDate\CheckPayDateRequest;

class CheckPayDateCommand
{
    private CheckPayDateUseCase $useCase;
    public function __construct(CheckPayDateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }
    public function __invoke(CheckPayDateRequest $request)
    {
        $response = ($this->useCase)($request);
        return $response;
    }
}
