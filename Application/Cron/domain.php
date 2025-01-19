<?php

error_reporting(E_ALL);

ini_set('display_errors', true);

use \Src\Infrastructure\Gateway\Database;

require_once __DIR__.'/../autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

$sql_q = "
SELECT d.name AS domain_name, days.value AS days
FROM data_domains AS d
LEFT JOIN data_times AS t ON t.id = d.time
LEFT JOIN data_days AS days ON days.id = d.days
WHERE t.id IN (SELECT t.id FROM data_times AS t WHERE 
DATE_FORMAT('2025-01-12 13:12:00', '%H') >= t.from)
ORDER BY t.from ASC, RAND();";

$res = Database::getDB()->query($sql_q)->fetch(PDO::FETCH_ASSOC);

$keys = json_decode(Database::getSetting()['domain_pay_text_search'] ?? '', true);

$whois = shell_exec("whois ".$res['domain_name']);
$whois_array = explode("\n", $whois);
if (!empty($whois_array)) {
    foreach ($whois_array AS $value) {
        $key_value = explode(":", $value);

        if (in_array($key_value[0], $keys)) {
            unset($key_value[0]);
            $now = new DateTime();
            $date = new DateTime(trim(implode(":", $key_value)));
            if ($now->diff($date)->days <= $res['days'] || !empty(Database::getSetting()['_test_notify'])) {
                // todo send notify
            }
        }
    }
}

