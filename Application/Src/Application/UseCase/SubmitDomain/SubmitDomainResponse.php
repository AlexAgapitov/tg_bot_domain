<?php

namespace Src\Application\UseCase\SubmitDomain;

class SubmitDomainResponse
{
    public string $id;
    public function __construct(
        string $id
    )
    {
        $this->id = $id;
    }
}