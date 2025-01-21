<?php

namespace Src\Application\UseCase\CheckPayDate;

use DateTime;
use ReflectionObject;
use Src\Domain\Repository\DomainRepositoryInterface;
use Src\Domain\Utils\NotifyInterface;
use Src\Domain\Utils\PayDateInterface;

class CheckPayDateUseCase
{
    private DomainRepositoryInterface $repository;
    private PayDateInterface $payDate;
    private NotifyInterface $notify;

    public function __construct(DomainRepositoryInterface $repository, PayDateInterface $payDate, NotifyInterface $notify)
    {
        $this->repository = $repository;
        $this->payDate = $payDate;
        $this->notify = $notify;
    }

    public function __invoke(CheckPayDateRequest $request): CheckPayDateResponse
    {
        $domain = $this->repository->findForCheck();

        if ($domain) {
            $pay_date = $this->payDate->getPayDate($domain->getName()->getValue());

            $old_pay_date = new DateTime($domain->getPayDate()->getValue());

            if (!$pay_date || (new DateTime($pay_date)) <= $old_pay_date) {
                $this->notify->payDate($domain->getUserId()->getValue(), $domain->getName()->getValue(), $domain->getPayDate()->getValue());
            }

            $this->repository->updateForCheck($domain, $pay_date);
        }

        return new CheckPayDateResponse(
            $pay_date ?? null
        );
    }
}