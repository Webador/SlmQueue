<?php

namespace SlmQueueTest\Job;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueueTest\Asset\SimpleQueue;
use SlmQueueTest\Asset\SimpleJob;

class QueueTest extends TestCase
{
    public function testCanPushThenPopJob()
    {
        $jobPluginManager = $this->getMock('SlmQueue\Job\JobPluginManager');
        $jobPluginManager->expects($this->once())
                         ->method('get')
                         ->with('SlmQueueTest\Asset\SimpleJob')
                         ->will($this->returnValue(new SimpleJob()));

        $queue = new SimpleQueue('queue', $jobPluginManager);

        $job = new SimpleJob();
        $job->setContent('Foo');

        $queue->push($job);

        $job = $queue->pop();

        $this->assertInstanceOf('SlmQueueTest\Asset\SimpleJob', $job);
        $this->assertEquals('Foo', $job->getContent());
    }
}
