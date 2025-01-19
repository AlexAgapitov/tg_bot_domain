<?php

namespace Src\Infrastructure\Repository;

use PDO;
use Src\Domain\Repository\TimesRepositoryInterface;
use Src\Infrastructure\Gateway\Database;

class TimesRepository implements TimesRepositoryInterface
{
    public function findAll(): array
    {
        $ans = [];
        $sql_q = "SELECT * FROM `data_times`";

        $rows = Database::getDB()->query($sql_q)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows ?? [] AS $row) {
            $ans[] = $row;
        }

        return $ans;
    }
}