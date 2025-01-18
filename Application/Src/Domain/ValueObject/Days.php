<?php

namespace Src\Domain\ValueObject;

class Days
{
    private int $value;

    public function __construct(int $value)
    {
        $this->assertValidDays($value);
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    private function assertValidDays(int $value): void
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('Invalid Days');
        }
    }
}
