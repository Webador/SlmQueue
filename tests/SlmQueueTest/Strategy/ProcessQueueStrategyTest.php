<?php

namespace SlmQueueTest\Listener\Strategy;

use PHPUnit_Framework_TestCase;
use SlmQueue\Strategy\ProcessQueueStrategy;
use SlmQueue\Worker\WorkerEvent;
use SlmQueueTest\Asset\SimpleJob;
use SlmQueueTest\Asset\SimpleWorker;

class ProcessQueueStrategyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ProcessQueueStrategy
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

        $worker = new SimpleWorker();

        $ev    = new WorkerEvent($worker, $queue);
        $job   = new SimpleJob();

        $ev->setJob($job);

        $this->listener = new ProcessQueueStrategy();
        $this->event    = $ev;
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        $this->assertInstanceOf('SlmQueue\Strategy\AbstractStrategy', $this->listener);
    }

    public function testListensToCorrectEvents()
    {
        $evm = $this->getMock('Zend\EventManager\EventManagerInterface');

        $priority = 1;

        $evm->expects($this->at(0))
            ->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS, array($this->listener, 'onJobPop'), $priority + 1);
        $evm->expects($this->at(1))
            ->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS, array($this->listener, 'onJobProcess'), $priority);

        $this->listener->attach($evm, $priority);
    }

    public function testOnJobPopHandler()
    {
        $this->listener->onJobPop($this->event);
        $this->assertFalse($this->event->shouldWorkerExitLoop());
    }
//
//    public function testOnJobProcessHandler()
//    {
//
//    }
}