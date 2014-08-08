<?php

namespace SlmQueueTest\Listener\Strategy;

use PHPUnit_Framework_TestCase;
use SlmQueue\Listener\Strategy\MaxRunsStrategy;
use SlmQueue\Worker\WorkerEvent;
use SlmQueueTest\Asset\SimpleJob;

class MaxRunsStrategyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MaxRunsStrategy
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

        $this->listener = new MaxRunsStrategy();
        $this->event    = $ev;
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        $this->assertInstanceOf('SlmQueue\Listener\Strategy\AbstractStrategy', $this->listener);
    }

    public function testMaxRunsDefault()
    {
        $this->assertTrue($this->listener->getMaxRuns() == 0);
    }

    public function testMaxRunsSetter()
    {
        $this->listener->setMaxRuns(2);

        $this->assertTrue($this->listener->getMaxRuns() == 2);
    }

    public function testListensToCorrectEvents()
    {
        $evm = $this->getMock('Zend\EventManager\EventManagerInterface');

        $evm->expects($this->at(0))->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS, array($this->listener, 'onStopConditionCheck'));
        $evm->expects($this->at(1))->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS_STATE, array($this->listener, 'onReportQueueState'));

        $this->listener->attach($evm);
    }

    public function testOnStopConditionCheckHandler()
    {
        $this->listener->setMaxRuns(3);

        $this->listener->onStopConditionCheck($this->event);
        $this->assertContains('1 jobs processed', $this->listener->onReportQueueState($this->event));
        $this->assertFalse($this->event->shouldWorkerExitLoop());

        $this->listener->onStopConditionCheck($this->event);
        $this->assertContains('2 jobs processed', $this->listener->onReportQueueState($this->event));
        $this->assertFalse($this->event->shouldWorkerExitLoop());

        $this->listener->onStopConditionCheck($this->event);
        $this->assertContains('maximum of 3 jobs processed', $this->listener->onReportQueueState($this->event));
        $this->assertTrue($this->event->shouldWorkerExitLoop());
    }
}
