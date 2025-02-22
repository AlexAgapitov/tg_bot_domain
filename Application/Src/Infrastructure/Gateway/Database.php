<?php

namespace Src\Infrastructure\Gateway;

use PDO;

class Database
{
    private static PDO $DB;
    private static array $settings;

    public static function getDB()
    {
        if (!isset(self::$DB)) {
            $dsn = 'mysql:host=' . $_ENV['db_host'] . ';dbname=' . $_ENV['db_name'];
            $user = $_ENV['db_user'];
            $password = $_ENV['db_pass'];
            self::$DB = new PDO($dsn, $user, $password, [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'", PDO::MYSQL_ATTR_LOCAL_INFILE => true]);
            self::$DB->query('SET SESSION group_concat_max_len = ~0;');
        }
        return self::$DB;
    }

    public static function getSetting(): array
    {
        if (!isset(self::$settings)) {
            $res = self::getDB()->query('SELECT * FROM `system_settings`');
            foreach ($res ?? [] AS $row) {
                self::$settings[$row['key']] = $row['value'];
            }
        }
        return self::$settings;
    }
}
