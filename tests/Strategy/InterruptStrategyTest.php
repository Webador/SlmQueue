<?php

namespace SlmQueueTest\Listener\Strategy;

use PHPUnit\Framework\TestCase;
use SlmQueue\Strategy\InterruptStrategy;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Event\ProcessQueueEvent;
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
        $this->queue    = $this->createMock(\SlmQueue\Queue\QueueInterface::class);
        $this->worker   = new SimpleWorker();
        $this->listener = new InterruptStrategy();
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        static::assertInstanceOf(\SlmQueue\Strategy\AbstractStrategy::class, $this->listener);
    }

    public function testListensToCorrectEventAtCorrectPriority()
    {
        $evm      = $this->createMock(\Laminas\EventManager\EventManagerInterface::class);
        $priority = 1;

        $evm->expects($this->at(0))->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_IDLE, [$this->listener, 'onStopConditionCheck'], $priority);
        $evm->expects($this->at(1))->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_QUEUE, [$this->listener, 'onStopConditionCheck'], -1000);
        $evm->expects($this->at(2))->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_STATE, [$this->listener, 'onReportQueueState'], $priority);

        $this->listener->attach($evm, $priority);
    }

    public function testOnStopConditionCheckHandler_NoSignal()
    {
        $result = $this->listener->onStopConditionCheck(new ProcessQueueEvent($this->worker, $this->queue));
        static::assertNull($result);
    }

    public function testOnStopConditionCheckHandler_SIGTERM()
    {
        $this->listener->onPCNTLSignal(SIGTERM);
        $result = $this->listener->onStopConditionCheck(new ProcessQueueEvent($this->worker, $this->queue));
        static::assertInstanceOf(ExitWorkerLoopResult::class, $result);
    }

    public function testOnStopConditionCheckHandler_SIGINT()
    {
        $this->listener->onPCNTLSignal(SIGINT);
        $result = $this->listener->onStopConditionCheck(new ProcessQueueEvent($this->worker, $this->queue));
        static::assertInstanceOf(ExitWorkerLoopResult::class, $result);
    }
}
