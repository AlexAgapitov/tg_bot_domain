<?php

namespace Src\Infrastructure\Repository;

use PDO;
use Src\Domain\Entity\Domain;
use Src\Domain\Repository\DomainRepositoryInterface;

class DomainRepository implements DomainRepositoryInterface
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

    public function findByUserId(int $user_id): iterable
    {
        $news = [];
        $sql_q = "SELECT * FROM `data_domains` WHERE `user_id` = $user_id";

        $rows = self::$Db->query($sql_q)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows ?? [] AS $row) {
            $news[] = $this->prepareDomain($row);
        }

        return $news;
    }

    public function findById(int $id): ?Domain
    {
        // TODO: Implement findById() method.
        return null;
    }

    public function save(Domain $domain): void
    {
        $sql_q = "INSERT INTO `data_domains` SET 
                   `name` = ".self::$Db->quote($domain->getName()->getValue()).",
                   `time` = ".$domain->getDays()->getValue().",
                   `days` = ".$domain->getTime()->getValue().",
                   `user_id` = ".$domain->getUserId()->getValue().",
                   `created` = NOW()";

        if (false === self::$Db->query($sql_q)) {
            switch (self::$Db->errorCode()) {
                case 23000:
                    throw new \Exception('Domain exist');
                    break;
                default:
                    throw new \Exception('Error save domain');
                    break;
            }
        }

        $id = self::$Db->lastInsertId();

        $reflectionProperty = new \ReflectionProperty(Domain::class, 'id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($domain, $id);
    }

    public function delete(Domain $domain): void
    {
        // TODO: Implement delete() method.
    }


    private function prepareDomain(array $values)
    {
        $reflectionClass = new \ReflectionClass(Domain::class);
        $domain = $reflectionClass->newInstanceWithoutConstructor();
        foreach ($reflectionClass->getProperties() AS $property) {
            $property_name = $property->getName();
            if (isset($values[$property_name])) {
                $reflectionProperty = $reflectionClass->getProperty($property_name);
                $type = $reflectionProperty->getType()->getName();
                if(!$property->getType()->isBuiltin()) //types names are strings
                    $value = new $type($values[$property_name]);
                else
                    $value = $values[$property_name];
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($domain, $value);
            }
        }
        return $domain;
    }
}