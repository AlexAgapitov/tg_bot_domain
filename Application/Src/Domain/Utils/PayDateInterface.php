<?php

namespace Src\Domain\Utils;

use DateTime;

interface PayDateInterface
{
    public function exec(string $domain): array;
    public function getPayDate(string $domain): ?DateTime;
}