<?php

namespace Src\Infrastructure\Repository;

use PDO;
use Src\Domain\Repository\DaysRepositoryInterface;

class DaysRepository implements DaysRepositoryInterface
{
    private static PDO $Db;

    public function __construct()
    {
        $dsn = 'mysql:host=' . $_ENV['db_host'] . ';dbname=' . $_ENV['db_name'];
        $user = $_ENV['db_user'];
        $password = $_ENV['db_pass'];
        self::$Db = new PDO($dsn, $user, $password, [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'", PDO::MYSQL_ATTR_LOCAL_INFILE => true]);
        self::$Db->query('SET SESSION group_concat_max_len = ~0;');
    }

    public function findAll(): array
    {
        $ans = [];
        $sql_q = "SELECT * FROM `data_days`";

        $rows = self::$Db->query($sql_q)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows ?? [] AS $row) {
            $ans[] = $row;
        }

        return $ans;
    }
}