<?php

namespace Src\Domain\ValueObject;

class Time
{
    private int $value;

    public function __construct(int $value)
    {
        $this->assertValidTime($value);
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    private function assertValidTime(int $value): void
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('Invalid Time');
        }
    }
}
