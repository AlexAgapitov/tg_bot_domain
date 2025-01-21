<?php

namespace Src\Application\UseCase\GetDomains;

class GetDomainsRequest
{
    public int $user_id;
    public function __construct(
        int $user_id
    )
    {
        $this->user_id = $user_id;
    }
}