<?php

namespace SlmQueueTest\Asset;

use SlmQueue\Job\AbstractJob;
use SlmQueue\Queue\Exception\RuntimeException;

class FailingJob extends AbstractJob
{
    public function execute(): ?int
    {
        throw new RuntimeException('I Failed');
    }
}
