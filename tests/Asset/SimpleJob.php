<?php

namespace SlmQueueTest\Asset;

use SlmQueue\Job\AbstractJob;

class SimpleJob extends AbstractJob
{
    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        // Just set some stupid metadata
        $this->setMetadata('foo', 'bar');

        return 'result';
    }
}
