<?php

namespace SlmQueueTest\Listener\Strategy;

use PHPUnit_Framework_TestCase;
use SlmQueue\Listener\Strategy\InterruptStrategy;
use SlmQueue\Worker\WorkerEvent;
use SlmQueueTest\Asset\SimpleJob;

class InterruptStrategyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var InterruptStrategy
     */
    protected $listener;

    public function setUp()
    {
        $this->listener = new InterruptStrategy();
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        $this->assertInstanceOf('SlmQueue\Listener\Strategy\AbstractStrategy', $this->listener);
    }

    public function testListensToCorrectEvents()
    {
        $evm = $this->getMock('Zend\EventManager\EventManagerInterface');

        $evm->expects($this->at(0))->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS_IDLE, array($this->listener, 'onStopConditionCheck'));
        $evm->expects($this->at(1))->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS_JOB_POST, array($this->listener, 'onStopConditionCheck'));

        $this->listener->attach($evm);
    }

    public function testOnStopConditionCheckHandler_NoSignal()
    {
        $queue = $this->getMockBuilder('SlmQueue\Queue\AbstractQueue')
            ->disableOriginalConstructor()
            ->getMock();

        $ev    = new WorkerEvent($queue);
        $job   = new SimpleJob();

        $ev->setJob($job);


        $this->listener->onStopConditionCheck($ev);
        $this->assertFalse($this->listener->getExitState());
        $this->assertFalse($ev->propagationIsStopped());
    }

    public function testOnStopConditionCheckHandler_SIGTERM()
    {
        $queue = $this->getMockBuilder('SlmQueue\Queue\AbstractQueue')
            ->disableOriginalConstructor()
            ->getMock();

        $ev    = new WorkerEvent($queue);
        $job   = new SimpleJob();

        $ev->setJob($job);


        $this->listener->onPCNTLSignal(SIGTERM);
        $this->listener->onStopConditionCheck($ev);
        $this->assertContains('interrupt by an external signal', $this->listener->getExitState());
        $this->assertTrue($ev->propagationIsStopped());
    }

    public function testOnStopConditionCheckHandler_SIGINT()
    {
        $queue = $this->getMockBuilder('SlmQueue\Queue\AbstractQueue')
            ->disableOriginalConstructor()
            ->getMock();

        $ev    = new WorkerEvent($queue);
        $job   = new SimpleJob();

        $ev->setJob($job);


        $this->listener->onPCNTLSignal(SIGTERM);
        $this->listener->onStopConditionCheck($ev);
        $this->assertContains('interrupt by an external signal', $this->listener->getExitState());
        $this->assertTrue($ev->propagationIsStopped());
    }
}
