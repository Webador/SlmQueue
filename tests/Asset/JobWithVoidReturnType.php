<?php

namespace SlmQueueTest\Asset;

use SlmQueue\Job\AbstractJob;

class JobWithVoidReturnType extends AbstractJob
{
    public function execute()
    {
        // Just set some stupid metadata
        $this->setMetadata('foo', 'bar');

        return 999;
    }
}
