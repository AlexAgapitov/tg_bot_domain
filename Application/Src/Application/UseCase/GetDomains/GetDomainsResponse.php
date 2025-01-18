<?php

namespace Src\Application\UseCase\GetDomains;

class GetDomainsResponse
{
    public array $domains;
    public function __construct(
        array $domains
    )
    {
        $this->domains = $domains;
    }
}