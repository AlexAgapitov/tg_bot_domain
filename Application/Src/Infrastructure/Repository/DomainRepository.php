<?php

namespace Src\Infrastructure\Repository;

use DateTime;
use PDO;
use Src\Domain\Entity\Domain;
use Src\Domain\Repository\DomainRepositoryInterface;
use Src\Infrastructure\Gateway\Database;

class DomainRepository implements DomainRepositoryInterface
{
    public function findByUserId(int $user_id): iterable
    {
        $ans = [];
        $sql_q = "SELECT * FROM `data_domains` WHERE `user_id` = $user_id";

        $rows = Database::getDB()->query($sql_q)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows ?? [] AS $row) {
            $ans[] = $this->prepareDomain($row);
        }

        return $ans;
    }

    public function findById(int $id): ?Domain
    {
        $ans = null;
        $sql_q = "SELECT * FROM `data_domains` WHERE `id` = $id";

        $row = Database::getDB()->query($sql_q)->fetchAll(PDO::FETCH_ASSOC)[0] ?? null;

        if (!empty($row)) {
            $ans = $this->prepareDomain($row);
        }

        return $ans;
    }

    public function save(Domain $domain): void
    {
        $sql_q = "INSERT INTO `data_domains` SET 
                   `name` = ".Database::getDB()->quote($domain->getName()->getValue()).",
                   `pay_date` = ".Database::getDB()->quote($domain->getPayDate()->getValue()).",
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

    public function findForCheck(): ?Domain
    {
        $ans = null;
        $date = new DateTime();
        $date = new DateTime('2026-12-12 21:00:00');
        $hours = $date->format('H');
        $date_Ymd = $date->format('Y-m-d');

        $sql_q = "
        SELECT r.*
        FROM data_domains AS r
        LEFT JOIN data_times AS t ON t.id = r.time
        LEFT JOIN data_days AS d ON d.id = r.days
        WHERE t.id IN (SELECT t.id FROM data_times AS t WHERE $hours >= t.from) 
            AND DATE(r.pay_date - INTERVAL d.value DAY) <= ".Database::getDB()->quote($date_Ymd)."
            AND (r.notify_date != ".Database::getDB()->quote($date_Ymd)." OR r.notify_date IS NULL)
        ORDER BY t.from ASC, RAND()
        LIMIT 1
        ";

        $row = Database::getDB()->query($sql_q)->fetch(PDO::FETCH_ASSOC) ?? null;

        if (!empty($row)) {
            $ans = $this->prepareDomain($row);
        }

        return $ans;
    }

    public function updateForCheck(Domain $domain, string $pay_date = null): void
    {
        $sql_q = "UPDATE data_domains SET 
                    `notify_date` = NOW()
                    ".(!empty($pay_date) ? ", `pay_date` = ".Database::getDB()->quote($pay_date) : "" )."
                    WHERE id = ".$domain->getId();

        if (false === Database::getDB()->query($sql_q)) {
            throw new \Exception('Error update domain');
        }
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