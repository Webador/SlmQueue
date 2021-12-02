<?php

namespace SlmQueueTest\Asset;

use SlmQueue\Job\AbstractJob;

class SimpleJob extends AbstractJob
{
    public function execute(): ?int
    {
        // Just set some stupid metadata
        $this->setMetadata('foo', 'bar');

        return 999;
    }
}
