<?php

namespace SlmQueue\Queue;

use SlmQueue\Job\JobInterface;
use SlmQueue\Job\JobPluginManager;
use SlmQueue\Worker\WorkerInterface;

interface QueueInterface
{
    public function getName(): string;

    public function getWorker(): WorkerInterface;

    public function getJobPluginManager(): JobPluginManager;

    public function push(JobInterface $job, array $options = []): void;

    public function pop(array $options = []): ?JobInterface;

    public function delete(JobInterface $job): void;
}
