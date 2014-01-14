<?php

namespace SlmQueueTest\Queue;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Job\JobPluginManager;
use SlmQueueTest\Asset\SimpleQueue;

class QueueAwareTraitTest extends TestCase
{
    /*
     * @covers SlmQueue\Queue\QueueAwareTrait::getQueue
     */
    public function testDefaultGetter()
    {
        /** @var $job \SlmQueue\Queue\QueueAwareTrait */
        $mock = $this->getMockForTrait('SlmQueue\Queue\QueueAwareTrait');

        $this->assertNull($mock->getQueue());
    }

    /*
     * @covers SlmQueue\Queue\QueueAwareTrait::setQueue
     */
    public function testSetter()
    {
        /** @var $job \SlmQueue\Queue\QueueAwareTrait */
        $job   = $this->getMockForTrait('SlmQueue\Queue\QueueAwareTrait');
        $queue = new SimpleQueue('name', new JobPluginManager());

        $job->setQueue($queue);

        $this->assertNotNull($job->getQueue());
        $this->assertEquals($queue, $job->getQueue());
    }

}
