<?php

namespace Src\Application\UseCase\DeleteDomain;

class DeleteDomainRequest
{
    public int $user_id;
    public string $domain_id;
    public function __construct(
        int $user_id,
        int $domain_id
    )
    {
        $this->user_id = $user_id;
        $this->domain_id = $domain_id;
    }
}