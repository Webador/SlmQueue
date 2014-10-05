<?php

namespace SlmQueueTest\Strategy;

use PHPUnit_Framework_TestCase;
use SlmQueue\Strategy\MaxMemoryStrategy;
use SlmQueue\Worker\WorkerEvent;
use SlmQueueTest\Asset\SimpleJob;

class MaxMemoryStrategyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MaxMemoryStrategy
     */
    protected $listener;

    /**
     * @var WorkerEvent
     */
    protected $event;

    public function setUp()
    {
        $queue = $this->getMockBuilder('SlmQueue\Queue\AbstractQueue')
            ->disableOriginalConstructor()
            ->getMock();
        $worker = $this->getMock('SlmQueue\Worker\WorkerInterface');

        $ev    = new WorkerEvent($worker, $queue);
        $job   = new SimpleJob();

        $ev->setJob($job);

        $this->listener = new MaxMemoryStrategy();
        $this->event    = $ev;
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        $this->assertInstanceOf('SlmQueue\Strategy\AbstractStrategy', $this->listener);
    }

    public function testMaxMemoryDefault()
    {
        $this->assertTrue($this->listener->getMaxMemory() == 0);
    }

    public function testMaxMemorySetter()
    {
        $this->listener->setMaxMemory(1024*25);

        $this->assertTrue($this->listener->getMaxMemory() == 1024*25);
    }

    public function testListensToCorrectEvents()
    {
        $evm = $this->getMock('Zend\EventManager\EventManagerInterface');

        $evm->expects($this->at(0))->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS_IDLE, array($this->listener, 'onStopConditionCheck'));
        $evm->expects($this->at(1))->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS_QUEUE, array($this->listener, 'onStopConditionCheck'));
        $evm->expects($this->at(2))->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS_STATE, array($this->listener, 'onReportQueueState'));

        $this->listener->attach($evm);
    }

    public function testContinueWhileThresholdNotExceeded()
    {
        $this->listener->setMaxMemory(1024*1024*1000);

        $this->listener->onStopConditionCheck($this->event);
        $this->assertContains('memory usage', $this->listener->onReportQueueState($this->event));
        $this->assertFalse($this->event->shouldExitWorkerLoop());
    }

    public function testRequestStopWhileThresholdExceeded()
    {
        $this->listener->setMaxMemory(1024);

        $this->listener->onStopConditionCheck($this->event);
        $this->assertContains(
            'memory threshold of 1kB exceeded (usage: ',
            $this->listener->onReportQueueState($this->event)
        );
        $this->assertTrue($this->event->shouldExitWorkerLoop());
    }
}
