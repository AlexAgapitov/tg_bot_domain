<?php

namespace Src\Infrastructure\Repository;

use PDO;
use Src\Domain\Entity\Domain;
use Src\Domain\Repository\DomainRepositoryInterface;
use Src\Infrastructure\Gateway\Database;

class DomainRepository implements DomainRepositoryInterface
{
    public function findByUserId(int $user_id): iterable
    {
        $news = [];
        $sql_q = "SELECT * FROM `data_domains` WHERE `user_id` = $user_id";

        $rows = Database::getDB()->query($sql_q)->fetchAll(PDO::FETCH_ASSOC);

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
                   `name` = ".Database::getDB()->quote($domain->getName()->getValue()).",
                   `pay_date` = ".Database::getDB()->quote($domain->getPayDate()->getValue()->format('Y-m-d H:i:s')).",
                   `time` = ".$domain->getDays()->getValue().",
                   `days` = ".$domain->getTime()->getValue().",
                   `user_id` = ".$domain->getUserId()->getValue().",
                   `created` = NOW()";

        if (false === Database::getDB()->query($sql_q)) {
            switch (Database::getDB()->errorCode()) {
                case 23000:
                    throw new \Exception('Domain exist');
                    break;
                default:
                    throw new \Exception('Error save domain');
                    break;
            }
        }

        $id = Database::getDB()->lastInsertId();

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