<?php

namespace Src\Application\UseCase\SubmitDomain;

class SubmitDomainResponse
{
    public int $id;
    public string $name;
    public string $payDate;
    public function __construct(
        int $id, string $name, string $payDate
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->payDate = $payDate;
    }
}