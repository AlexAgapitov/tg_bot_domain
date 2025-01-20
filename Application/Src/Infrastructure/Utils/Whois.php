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

    public function getPayDate(string $domain): ?DateTime
    {
        $keys = json_decode(Database::getSetting()['domain_pay_text_search'] ?? '', true);
        $payDate = null;
        $answer = $this->exec($domain);

        if (!empty($answer)) {
            foreach ($answer as $key => $value) {
                if (in_array($key, $keys)) {
                    $payDate = new DateTime($value);
                    break;
                }
            }
        }

        if (empty($payDate)) {
            throw new \Exception('Pay date not found');
        }

        return $payDate;
    }
}