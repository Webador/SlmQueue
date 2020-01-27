<?php

namespace SlmQueueTest\Strategy;

use PHPUnit_Framework_TestCase;
use SlmQueue\Strategy\LogJobStrategy;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Event\ProcessJobEvent;
use SlmQueue\Worker\Event\ProcessStateEvent;
use SlmQueueTest\Asset\SimpleJob;
use SlmQueueTest\Asset\SimpleWorker;

class LogJobTest extends PHPUnit_Framework_TestCase
{
    protected $queue;
    protected $worker;
    protected $console;
    /** @var LogJobStrategy */
    protected $listener;

    public function setUp()
    {
        $this->queue    = $this->getMock(\SlmQueue\Queue\QueueInterface::class);
        $this->worker   = new SimpleWorker();
        $this->console  = $this->getMock('Laminas\Console\Adapter\AdapterInterface');
        $this->listener = new LogJobStrategy($this->console);
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        static::assertInstanceOf(\SlmQueue\Strategy\AbstractStrategy::class, $this->listener);
    }

    public function testListensToCorrectEventAtCorrectPriority()
    {
        $evm      = $this->getMock(\Laminas\EventManager\EventManagerInterface::class);
        $priority = 1;

        $evm->expects($this->at(0))->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_JOB, [$this->listener, 'onLogJobProcessStart'], 1000);
        $evm->expects($this->at(1))->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_JOB, [$this->listener, 'onLogJobProcessDone'], -1000);

        $this->listener->attach($evm, $priority);
    }

    public function testOnLogJobProcessStart_SendsOutputToConsole()
    {
        $this->console->expects($this->once())->method('write')
            ->with('Processing job SlmQueueTest\Asset\SimpleJob...');

        $this->listener->onLogJobProcessStart(new ProcessJobEvent(new SimpleJob(), $this->worker, $this->queue));
    }

    public function testOnLogJobProcessStart_DoesNotGenerateState()
    {
        $this->listener->onLogJobProcessStart(new ProcessJobEvent(new SimpleJob(), $this->worker, $this->queue));

        static::assertFalse($this->listener->onReportQueueState(new ProcessStateEvent($this->worker)));
    }

    public function testOnLogJobProcessStart_DoesNotHaltPropagation()
    {
        $result = $this->listener->onLogJobProcessStart(new ProcessJobEvent(new SimpleJob(), $this->worker,
            $this->queue));

        static::assertNull($result);
    }

    public function testOnLogJobProcessDone_SendsOutputToConsole()
    {
        $this->console->expects($this->once())->method('writeLine')
            ->with('Done!');

        $this->listener->onLogJobProcessDone(new ProcessJobEvent(new SimpleJob(), $this->worker, $this->queue));
    }

    public function testOnLogJobProcessDone_DoesNotGenerateState()
    {
        $this->listener->onLogJobProcessDone(new ProcessJobEvent(new SimpleJob(), $this->worker, $this->queue));

        static::assertFalse($this->listener->onReportQueueState(new ProcessStateEvent($this->worker)));
    }

    public function testOnLogJobProcessDone_DoesNotHaltPropagation()
    {
        $result = $this->listener->onLogJobProcessDone(new ProcessJobEvent(new SimpleJob(), $this->worker,
            $this->queue));

        static::assertNull($result);
    }
}
