<?php

namespace Src\Domain\Entity;

use Src\Domain\ValueObject\PayDate;
use Src\Domain\ValueObject\Time;
use Src\Domain\ValueObject\UserId;
use Src\Domain\ValueObject\Name;
use Src\Domain\ValueObject\Days;

class Domain
{
    private UserId $user_id;
    private Name $name;
    private Time $time;
    private Days $days;
    private PayDate $pay_date;
    private ?int $id;

    public function __construct(UserId $user_id, Name $name, Time $time, Days $days, PayDate $pay_date)
    {
        $this->user_id = $user_id;
        $this->name = $name;
        $this->time = $time;
        $this->days = $days;
        $this->pay_date = $pay_date;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getTime(): Time
    {
        return $this->time;
    }

    public function getDays(): Days
    {
        return $this->days;
    }

    public function getUserId(): UserId
    {
        return $this->user_id;
    }

    public function getPayDate(): PayDate
    {
        return $this->pay_date;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
