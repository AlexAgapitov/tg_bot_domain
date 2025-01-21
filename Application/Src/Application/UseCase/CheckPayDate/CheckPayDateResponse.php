<?php

namespace Src\Application\UseCase\CheckPayDate;

class CheckPayDateResponse
{
    public ?string $pay_date;
    public function __construct(
        ?string $pay_date
    )
    {
        $this->pay_date = $pay_date;
    }
}