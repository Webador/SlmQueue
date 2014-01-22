<?php

namespace SlmQueueTest\Queue;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Job\JobPluginManager;
use SlmQueueTest\Asset\QueueAwareTraitJob;
use SlmQueueTest\Asset\SimpleQueue;

class QueueAwareTraitTest extends TestCase
{
    /**
     * @var QueueAwareTraitJob $traitObject
     */
    private $job;

    public function setUp()
    {
        if (version_compare(phpversion() , '5.4', 'lt')) {
            $this->markTestSkipped(
                'Traits are not available in php53.'
            );
            return;
        }

        $this->job = new QueueAwareTraitJob();
    }

    public function testDefaultGetter() {
        $this->assertNull($this->job->getQueue());
    }

    public function testSetter() {
        $queue = new SimpleQueue('name', new JobPluginManager());
        $this->job->setQueue($queue);

        $this->assertNotNull($this->job->getQueue());
        $this->assertEquals($queue, $this->job->getQueue());
    }
}
