<?php

namespace Src\Application\UseCase\GetDays;

use Src\Application\Gateway\RedisGatewayInterface;
use Src\Domain\Repository\DaysRepositoryInterface;

class GetDaysUseCase
{
    private DaysRepositoryInterface $repository;
    private RedisGatewayInterface $redis;

    public function __construct(DaysRepositoryInterface $repository, RedisGatewayInterface $redis)
    {
        $this->repository = $repository;
        $this->redis = $redis;
    }

    public function __invoke(GetDaysRequest $request): GetDaysResponse
    {
        $times = $this->redis->getDays();

        if (is_null($times)) {
            $times = $this->repository->findAll();
            $this->redis->setDays($times);
        }

        return new GetDaysResponse(
            $times
        );
    }
}