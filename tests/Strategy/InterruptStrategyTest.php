<?php

namespace SlmQueueTest\Listener\Strategy;

use Laminas\EventManager\EventManagerInterface;
use PHPUnit\Framework\TestCase;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Strategy\AbstractStrategy;
use SlmQueue\Strategy\InterruptStrategy;
use SlmQueue\Worker\Event\ProcessQueueEvent;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Result\ExitWorkerLoopResult;
use SlmQueueTest\Asset\SimpleWorker;

class InterruptStrategyTest extends TestCase
{
    protected $queue;
    protected $worker;
    /** @var InterruptStrategy */
    protected $listener;

    public function setUp(): void
    {
        $this->queue = $this->createMock(QueueInterface::class);
        $this->worker = new SimpleWorker();
        $this->listener = new InterruptStrategy();
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        static::assertInstanceOf(AbstractStrategy::class, $this->listener);
    }

    public function testListensToCorrectEventAtCorrectPriority()
    {
        $evm = $this->createMock(EventManagerInterface::class);
        $priority = 1;

        $evm->expects($this->at(0))->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_IDLE, [$this->listener, 'onStopConditionCheck'], $priority);
        $evm->expects($this->at(1))->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_QUEUE, [$this->listener, 'onStopConditionCheck'], -1000);
        $evm->expects($this->at(2))->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_STATE, [$this->listener, 'onReportQueueState'], $priority);

        $this->listener->attach($evm, $priority);
    }

    public function testOnStopConditionCheckHandlerNoSignal()
    {
        $result = $this->listener->onStopConditionCheck(new ProcessQueueEvent($this->worker, $this->queue));
        static::assertNull($result);
    }

    public function testOnStopConditionCheckHandlerSIGTERM()
    {
        $this->listener->onPCNTLSignal(SIGTERM);
        $result = $this->listener->onStopConditionCheck(new ProcessQueueEvent($this->worker, $this->queue));
        static::assertInstanceOf(ExitWorkerLoopResult::class, $result);
    }

    public function testOnStopConditionCheckHandlerSIGINT()
    {
        $this->listener->onPCNTLSignal(SIGINT);
        $result = $this->listener->onStopConditionCheck(new ProcessQueueEvent($this->worker, $this->queue));
        static::assertInstanceOf(ExitWorkerLoopResult::class, $result);
    }
}
