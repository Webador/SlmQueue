<?php

namespace SlmQueueTest\Strategy;

use PHPUnit_Framework_TestCase;
use SlmQueue\Strategy\LogJobStrategy;
use SlmQueue\Worker\WorkerEvent;
use SlmQueueTest\Asset\SimpleJob;

class LogJobTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var LogJobStrategy
     */
    protected $listener;

    /**
     * @var WorkerEvent
     */
    protected $event;

    /**
     * @var \Zend\Console\Adapter\AdapterInterface
     */
    protected $console;

    public function setUp()
    {
        $queue = $this->getMockBuilder('SlmQueue\Queue\AbstractQueue')
            ->disableOriginalConstructor()
            ->getMock();
        $worker = $this->getMock('SlmQueue\Worker\WorkerInterface');

        $ev    = new WorkerEvent($worker, $queue);
        $job   = new SimpleJob();

        $ev->setJob($job);

        $this->console  = $this->getMock('Zend\Console\Adapter\AdapterInterface');
        $this->listener = new LogJobStrategy($this->console);
        $this->event    = $ev;
    }

    public function tearDown()
    {

    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        $this->assertInstanceOf('SlmQueue\Strategy\AbstractStrategy', $this->listener);
    }

    public function testListensToCorrectEvents()
    {
        $evm = $this->getMock('Zend\EventManager\EventManagerInterface');

        $evm->expects($this->at(0))->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS_JOB, [$this->listener, 'onLogJobProcessStart']);
        $evm->expects($this->at(1))->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS_JOB, [$this->listener, 'onLogJobProcessDone']);

        $this->listener->attach($evm);
    }

    public function testOnLogJobProcessStart_SendsOutputToConsole()
    {
        $this->console->expects($this->once())->method('write')
            ->with('Processing job SlmQueueTest\Asset\SimpleJob...');

        $this->listener->onLogJobProcessStart($this->event);
    }
    public function testOnLogJobProcessStart_DoesNotGenerateState()
    {
        $this->listener->onLogJobProcessStart($this->event);

        $this->assertFalse($this->listener->onReportQueueState($this->event));
    }
    public function testOnLogJobProcessStart_DoesNotHaltPropagation()
    {
        $this->listener->onLogJobProcessStart($this->event);

        $this->assertFalse($this->event->shouldExitWorkerLoop());
    }

    public function testOnLogJobProcessDone_SendsOutputToConsole()
    {
        $this->console->expects($this->once())->method('writeLine')
            ->with('Done!');

        $this->listener->onLogJobProcessDone($this->event);
    }
    public function testOnLogJobProcessDone_DoesNotGenerateState()
    {
        $this->listener->onLogJobProcessDone($this->event);

        $this->assertFalse($this->listener->onReportQueueState($this->event));
    }
    public function testOnLogJobProcessDone_DoesNotHaltPropagation()
    {
        $this->listener->onLogJobProcessDone($this->event);

        $this->assertFalse($this->event->shouldExitWorkerLoop());
    }
}
