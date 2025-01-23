<?php

namespace Src\Domain\Repository;

use Src\Domain\Entity\Domain;

interface DomainRepositoryInterface
{
    /**
     * @return Domain[]
     */
    public function findByUserId(int $user_id): iterable;

    public function findById(int $id): ?Domain;

    public function save(Domain $domain): void;

    public function findForCheck(): ?Domain;

    public function updateForCheck(Domain $domain, string $pay_date = null): void;

    public function delete(int $id): void;
}
