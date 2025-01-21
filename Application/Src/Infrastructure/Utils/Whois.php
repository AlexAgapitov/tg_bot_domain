<?php

namespace Src\Infrastructure\Utils;

use DateTime;
use Src\Domain\Utils\PayDateInterface;
use Src\Infrastructure\Gateway\Database;

class Whois implements PayDateInterface
{
    public function exec(string $domain): array
    {
        $answer = [];
        $whois = shell_exec("whois ".$domain);
        $whois_array = explode("\n", $whois);
        if (!empty($whois_array)) {
            foreach ($whois_array as $value) {
                $key_value = explode(":", $value);
                $key = $key_value[0];
                unset($key_value[0]);
                $answer[$key] = trim(implode(":", $key_value));
            }
        }
        return $answer;
    }

    public function getPayDate(string $domain): ?string
    {
        $keys = json_decode(Database::getSetting()['domain_pay_text_search'] ?? '', true);
        $pay_date = null;
        $answer = $this->exec($domain);

        if (!empty($answer)) {
            foreach ($answer as $key => $value) {
                if (in_array($key, $keys)) {
                    $pay_date = (new DateTime($value))->format('Y-m-d H:i:s');
                    break;
                }
            }
        }

        return $pay_date;
    }
}