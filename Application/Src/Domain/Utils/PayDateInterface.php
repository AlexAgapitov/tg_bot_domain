<?php

namespace Src\Domain\Utils;

interface PayDateInterface
{
    public function exec(string $domain): array;
    public function getPayDate(string $domain): ?string;
}