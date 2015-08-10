<?php

namespace SlmQueueTest\Asset;

use SlmQueue\Job\AbstractJob;
use SlmQueue\Queue\Exception\RuntimeException;

class FailingJob extends AbstractJob
{
    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        throw new RuntimeException('I Failed');
    }
}
