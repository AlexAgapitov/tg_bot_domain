<?php

namespace Src\Domain\ValueObject;

class Name
{
    private string $value;

    public function __construct(string $value)
    {
        $this->assertValidName($value);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function assertValidName(string $value): void
    {
        if (mb_strlen($value) === 0) {
            throw new \InvalidArgumentException('Invalid Name');
        }
    }
}
