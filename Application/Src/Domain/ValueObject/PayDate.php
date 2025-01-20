<?php

namespace Src\Domain\ValueObject;

use DateTime;

class PayDate
{
    private DateTime $value;

    public function __construct(DateTime $value)
    {
        $this->value = $value;
    }

    public function getValue(): DateTime
    {
        return $this->value;
    }
}
