<?php

namespace SlmQueueTest\Controller\Plugin;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Controller\Plugin\QueuePlugin;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueue\Job\JobPluginManager;
use SlmQueueTest\Asset\QueueAwareJob;
use SlmQueueTest\Asset\SimpleQueue;
use SlmQueueTest\Asset\SimpleJob;

class QueueTest extends TestCase
{
    public function testPluginCreatesQueueFromPluginManager()
    {
        $queuePluginManager = $this->getMock('SlmQueue\Queue\QueuePluginManager');
        $jobPluginManager   = $this->getMock('SlmQueue\Job\JobPluginManager');

        $queue = new SimpleQueue('DefaultQueue', $jobPluginManager);

        $queuePluginManager->expects($this->once())
                           ->method('has')
                           ->with('DefaultQueue')
                           ->will($this->returnValue(true));

        $queuePluginManager->expects($this->once())
                           ->method('get')
                           ->with('DefaultQueue')
                           ->will($this->returnValue($queue));

        $plugin = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke('DefaultQueue');
    }

    public function testPluginThrowsExceptionWhenQueueDoesNotExists()
    {
        $queuePluginManager = $this->getMock('SlmQueue\Queue\QueuePluginManager');
        $jobPluginManager   = $this->getMock('SlmQueue\Job\JobPluginManager');

        $queuePluginManager->expects($this->once())
            ->method('has')
            ->with('DefaultQueue')
            ->will($this->returnValue(false));

        $this->setExpectedException('SlmQueue\Controller\Exception\QueueNotFoundException');

        $plugin = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke('DefaultQueue');
    }

    public function testPluginThrowsExceptionWhenNoQueueIsSet()
    {
        $queuePluginManager = $this->getMock('SlmQueue\Queue\QueuePluginManager');
        $jobPluginManager   = $this->getMock('SlmQueue\Job\JobPluginManager');
        $plugin             = new QueuePlugin($queuePluginManager, $jobPluginManager);

        $this->setExpectedException('SlmQueue\Controller\Exception\QueueNotFoundException');
        $plugin->push('TestJob');

    }

    public function testPluginPushesJobIntoQueue()
    {
        $queuePluginManager = new QueuePluginManager;
        $jobPluginManager   = new JobPluginManager;

        $name  = 'DefaultQueue';
        $queue = $this->getMock('SlmQueueTest\Asset\SimpleQueue', array('push'), array($name, $jobPluginManager));
        $job   = new SimpleJob;

        $queue->expects($this->once())
              ->method('push')
              ->with($job)
              ->will($this->returnValue($job));
        $queuePluginManager->setService($name, $queue);
        $jobPluginManager->setService('SimpleJob', $job);

        $plugin = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke($name);

        $result = $plugin->push('SimpleJob');
        $this->assertSame($job, $result);
    }

    public function testPayloadCanBeInjectedViaPlugin()
    {
        $queuePluginManager = new QueuePluginManager;
        $jobPluginManager   = new JobPluginManager;

        $name  = 'DefaultQueue';
        $queue = $this->getMock('SlmQueueTest\Asset\SimpleQueue', array('push'), array($name, $jobPluginManager));
        $job   = new SimpleJob;

        $queue->expects($this->once())
              ->method('push')
              ->with($job)
              ->will($this->returnValue($job));
        $queuePluginManager->setService($name, $queue);
        $jobPluginManager->setService('SimpleJob', $job);

        $plugin  = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke($name);

        $payload = array('foo' => 'bar');
        $result  = $plugin->push('SimpleJob', $payload);

        $this->assertSame($payload, $result->getContent());
    }
}
