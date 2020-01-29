<?php

namespace SlmQueueTest\Queue;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use SlmQueue\Job\JobPluginManager;
use SlmQueueTest\Asset\QueueAwareTraitJob;
use SlmQueueTest\Asset\SimpleQueue;

class QueueAwareTraitTest extends TestCase
{
    /**
     * @var QueueAwareTraitJob $traitObject
     */
    private $job;

    public function setUp(): void
    {
        $this->job = new QueueAwareTraitJob();
    }

    public function testSetter(): void
    {
        $serviceManager = new ServiceManager();
        $jobPluginManager = new JobPluginManager($serviceManager);
        $queue = new SimpleQueue('name', $jobPluginManager);
        $this->job->setQueue($queue);

        static::assertNotNull($this->job->getQueue());
        static::assertEquals($queue, $this->job->getQueue());
    }
}
