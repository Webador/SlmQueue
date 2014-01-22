<?php

namespace SlmQueueTest\Controller\Plugin;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Controller\Plugin\Queue as QueuePlugin;
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
                           ->method('get')
                           ->with('DefaultQueue')
                           ->will($this->returnValue($queue));

        $plugin = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke('DefaultQueue');
    }

    public function testPluginPushesJobIntoQueue()
    {
        $queuePluginManager = $this->getMock('SlmQueue\Queue\QueuePluginManager');
        $jobPluginManager   = $this->getMock('SlmQueue\Job\JobPluginManager');

        $job   = new SimpleJob;
        $queue = new SimpleQueue('DefaultQueue', $jobPluginManager);

        $jobPluginManager->expects($this->once())
                         ->method('get')
                         ->with('SimpleJob')
                         ->will($this->returnValue($job));

        $queuePluginManager->expects($this->once())
                           ->method('get')
                           ->with('DefaultQueue')
                           ->will($this->returnValue($queue));

        $queuePluginManager->expects($this->once())
                           ->method('push')
                           ->with($job);

        $plugin = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke('DefaultQueue');

        $result = $plugin->push('SimpleJob');
    }
}
