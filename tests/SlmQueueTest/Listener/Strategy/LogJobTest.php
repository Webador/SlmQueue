<?php

namespace SlmQueueTest\Listener\Strategy;

use PHPUnit_Framework_TestCase;
use SlmQueue\Listener\Strategy\LogJobStrategy;
use SlmQueue\Worker\WorkerEvent;
use SlmQueueTest\Asset\SimpleJob;

class LogJobTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var LogJobStrategy
     */
    protected $listener;

    public function setUp()
    {
        $this->listener = new LogJobStrategy();
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        $this->assertInstanceOf('SlmQueue\Listener\Strategy\AbstractStrategy', $this->listener);
    }

    public function testListensToCorrectEvents()
    {
        $evm = $this->getMock('Zend\EventManager\EventManagerInterface');

        $evm->expects($this->at(0))->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS_JOB_PRE, array($this->listener, 'onLogJobProcessStart'));
        $evm->expects($this->at(1))->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS_JOB_POST, array($this->listener, 'onLogJobProcessDone'));

        $this->listener->attach($evm);
    }
//
//    public function testOnStopConditionCheckHandler()
//    {
//        $queue = $this->getMockBuilder('SlmQueue\Queue\AbstractQueue')
//            ->disableOriginalConstructor()
//            ->getMock();
//
//        $ev    = new WorkerEvent($queue);
//        $job   = new SimpleJob();
//
//        $ev->setJob($job);
//
//        $this->listener->setMaxMemory(1024*1024*1000);
//
//        $this->listener->onStopConditionCheck($ev);
//        $this->assertContains('memory usage', $this->listener->getExitState());
//        $this->assertFalse($ev->propagationIsStopped());
//
//        $this->listener->setMaxMemory(1024);
//
//        $this->listener->onStopConditionCheck($ev);
//        $this->assertContains('memory threshold of 1kB exceeded (usage: ', $this->listener->getExitState());
//        $this->assertTrue($ev->propagationIsStopped());
//
//    }
}
