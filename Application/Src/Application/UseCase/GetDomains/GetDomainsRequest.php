<?php

namespace Src\Application\UseCase\GetDomains;

class GetDomainsRequest
{
    public int $userId;
    public function __construct(
        int $userId
    )
    {
        $this->userId = $userId;
    }
}