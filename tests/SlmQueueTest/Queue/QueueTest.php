<?php

namespace SlmQueueTest\Queue;

use DateTime;
use PHPUnit_Framework_TestCase as TestCase;
use SlmQueueTest\Asset\QueueAwareJob;
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

    public function testCanCreateJobWithStringName()
    {
        $job = new SimpleJob();
        $jobPluginManager = $this->getMock('SlmQueue\Job\JobPluginManager');
        $jobPluginManager->expects($this->once())
                         ->method('get')
                         ->with('SimpleJob')
                         ->will($this->returnValue($job));

        $queue  = new SimpleQueue('queue', $jobPluginManager);
        $result = $queue->createJob('SimpleJob');

        $expected = spl_object_hash($job);
        $actual   = spl_object_hash($result);
        $this->assertEquals($expected, $actual);
    }

    public function testCanCreateJobWithContent()
    {
        $job = $this->getMock('SlmQueue\Job\JobInterface');
        $job->expects($this->once())
            ->method('setContent')
            ->with('Foo')
            ->will($this->returnValue($job));

        $jobPluginManager = $this->getMock('SlmQueue\Job\JobPluginManager');
        $jobPluginManager->expects($this->once())
                         ->method('get')
                         ->with('SimpleJob')
                         ->will($this->returnValue($job));

        $queue  = new SimpleQueue('queue', $jobPluginManager);
        $result = $queue->createJob('SimpleJob', serialize('Foo'));
    }

    public function testCanCreateJobWithMetadata()
    {
        $job = $this->getMock('SlmQueue\Job\JobInterface');
        $job->expects($this->once())
            ->method('setContent')
            ->will($this->returnValue($job));

        $job->expects($this->once())
            ->method('setMetadata')
            ->with(array('foo' => 'Bar'))
            ->will($this->returnValue($job));

        $jobPluginManager = $this->getMock('SlmQueue\Job\JobPluginManager');
        $jobPluginManager->expects($this->once())
                         ->method('get')
                         ->with('SimpleJob')
                         ->will($this->returnValue($job));

        $queue  = new SimpleQueue('queue', $jobPluginManager);
        $result = $queue->createJob('SimpleJob', null, array('foo' => 'Bar'));
    }

    public function testCreateQueueAwareJob()
    {
        $job = new QueueAwareJob();

        $jobPluginManager = $this->getMock('SlmQueue\Job\JobPluginManager');
        $jobPluginManager->expects($this->once())
                         ->method('get')
                         ->with('SlmQueueTest\Asset\QueueAwareJob')
                         ->will($this->returnValue($job));

        $queue = new SimpleQueue('queue', $jobPluginManager);

        $result = $queue->createJob('SlmQueueTest\Asset\QueueAwareJob');

        $this->assertSame($queue, $result->getQueue());
    }
}
