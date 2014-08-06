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

    public function setUp()
    {
        $this->listener = new MaxRunsStrategy();
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
            ->with(WorkerEvent::EVENT_PROCESS_JOB_POST, array($this->listener, 'onStopConditionCheck'));

        $this->listener->attach($evm);
    }

    public function testOnStopConditionCheckHandler()
    {
        $queue = $this->getMockBuilder('SlmQueue\Queue\AbstractQueue')
            ->disableOriginalConstructor()
            ->getMock();

        $ev    = new WorkerEvent($queue);
        $job   = new SimpleJob();

        $ev->setJob($job);

        $this->listener->setMaxRuns(3);


        $this->listener->onStopConditionCheck($ev);
        $this->assertContains('1 jobs processed', $this->listener->getState());
        $this->assertFalse($ev->propagationIsStopped());

        $this->listener->onStopConditionCheck($ev);
        $this->assertContains('2 jobs processed', $this->listener->getState());
        $this->assertFalse($ev->propagationIsStopped());

        $this->listener->onStopConditionCheck($ev);
        $this->assertContains('maximum of 3 jobs processed', $this->listener->getState());
        $this->assertTrue($ev->propagationIsStopped());
    }
}
