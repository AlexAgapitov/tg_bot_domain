<?php

namespace Src\Domain\Entity;

use Src\Domain\ValueObject\PayDate;
use Src\Domain\ValueObject\Time;
use Src\Domain\ValueObject\UserId;
use Src\Domain\ValueObject\Name;
use Src\Domain\ValueObject\Days;

class Domain
{
    private UserId $userId;
    private Name $name;
    private Time $time;
    private Days $days;
    private PayDate $payDate;
    private ?int $id;

    public function __construct(UserId $userId, Name $name, Time $time, Days $days, PayDate $payDate)
    {
        $this->userId = $userId;
        $this->name = $name;
        $this->time = $time;
        $this->days = $days;
        $this->payDate = $payDate;
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
        return $this->userId;
    }

    public function getPayDate(): PayDate
    {
        return $this->payDate;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
