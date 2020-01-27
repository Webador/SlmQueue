<?php

namespace SlmQueueTest\Strategy;

use Laminas\EventManager\EventManagerInterface;
use PHPUnit\Framework\TestCase;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Strategy\AbstractStrategy;
use SlmQueue\Strategy\LogJobStrategy;
use SlmQueue\Worker\Event\ProcessJobEvent;
use SlmQueue\Worker\Event\ProcessStateEvent;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueueTest\Asset\SimpleJob;
use SlmQueueTest\Asset\SimpleWorker;

class LogJobTest extends TestCase
{
    protected $queue;
    protected $worker;
    protected $console;
    /** @var LogJobStrategy */
    protected $listener;

    public function setUp(): void
    {
        $this->queue = $this->createMock(QueueInterface::class);
        $this->worker = new SimpleWorker();
        $this->console = $this->createMock('Laminas\Console\Adapter\AdapterInterface');
        $this->listener = new LogJobStrategy($this->console);
    }

    public function testListenerInstanceOfAbstractStrategy(): void
    {
        static::assertInstanceOf(AbstractStrategy::class, $this->listener);
    }

    public function testListensToCorrectEventAtCorrectPriority(): void
    {
        $evm = $this->createMock(EventManagerInterface::class);
        $priority = 1;

        $evm->expects($this->at(0))->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_JOB, [$this->listener, 'onLogJobProcessStart'], 1000);
        $evm->expects($this->at(1))->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_JOB, [$this->listener, 'onLogJobProcessDone'], -1000);

        $this->listener->attach($evm, $priority);
    }

    public function testOnLogJobProcessStartSendsOutputToConsole(): void
    {
        $this->console->expects($this->once())->method('write')
            ->with('Processing job SlmQueueTest\Asset\SimpleJob...');

        $this->listener->onLogJobProcessStart(new ProcessJobEvent(new SimpleJob(), $this->worker, $this->queue));
    }

    public function testOnLogJobProcessStartDoesNotGenerateState()
    {
        $this->listener->onLogJobProcessStart(new ProcessJobEvent(new SimpleJob(), $this->worker, $this->queue));

        static::assertFalse($this->listener->onReportQueueState(new ProcessStateEvent($this->worker)));
    }

    public function testOnLogJobProcessStartDoesNotHaltPropagation(): void
    {
        $result = $this->listener->onLogJobProcessStart(new ProcessJobEvent(
            new SimpleJob(),
            $this->worker,
            $this->queue
        ));

        static::assertNull($result);
    }

    public function testOnLogJobProcessDoneSendsOutputToConsole(): void
    {
        $this->console->expects($this->once())->method('writeLine')
            ->with('Done!');

        $this->listener->onLogJobProcessDone(new ProcessJobEvent(new SimpleJob(), $this->worker, $this->queue));
    }

    public function testOnLogJobProcessDoneDoesNotGenerateState(): void
    {
        $this->listener->onLogJobProcessDone(new ProcessJobEvent(new SimpleJob(), $this->worker, $this->queue));

        static::assertFalse($this->listener->onReportQueueState(new ProcessStateEvent($this->worker)));
    }

    public function testOnLogJobProcessDoneDoesNotHaltPropagation(): void
    {
        $result = $this->listener->onLogJobProcessDone(new ProcessJobEvent(
            new SimpleJob(),
            $this->worker,
            $this->queue
        ));

        static::assertNull($result);
    }
}
