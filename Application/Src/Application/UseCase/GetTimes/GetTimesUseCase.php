<?php

namespace Src\Application\UseCase\GetTimes;

use Src\Application\Gateway\RedisGatewayInterface;
use Src\Domain\Repository\TimesRepositoryInterface;

class GetTimesUseCase
{
    private TimesRepositoryInterface $repository;
    private RedisGatewayInterface $redis;

    public function __construct(TimesRepositoryInterface $repository, RedisGatewayInterface $redis)
    {
        $this->repository = $repository;
        $this->redis = $redis;
    }

    public function __invoke(GetTimesRequest $request): GetTimesResponse
    {
        $times = $this->redis->getTimes();

        if (is_null($times)) {
            $times = $this->repository->findAll();
            $this->redis->setTimes($times);
        }

        return new GetTimesResponse(
            $times
        );
    }
}