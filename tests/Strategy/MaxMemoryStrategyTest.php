<?php

namespace SlmQueueTest\Strategy;

use Laminas\EventManager\EventManagerInterface;
use PHPUnit\Framework\TestCase;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Strategy\AbstractStrategy;
use SlmQueue\Strategy\MaxMemoryStrategy;
use SlmQueue\Worker\Event\ProcessQueueEvent;
use SlmQueue\Worker\Event\ProcessStateEvent;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Result\ExitWorkerLoopResult;
use SlmQueueTest\Asset\SimpleWorker;

class MaxMemoryStrategyTest extends TestCase
{
    protected $queue;
    protected $worker;
    /** @var MaxMemoryStrategy */
    protected $listener;

    public function setUp(): void
    {
        $this->queue = $this->createMock(QueueInterface::class);
        $this->worker = new SimpleWorker();
        $this->listener = new MaxMemoryStrategy();
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        static::assertInstanceOf(AbstractStrategy::class, $this->listener);
    }

    public function testMaxMemoryDefault()
    {
        static::assertTrue($this->listener->getMaxMemory() == 0);
    }

    public function testMaxMemorySetter()
    {
        $this->listener->setMaxMemory(1024 * 25);

        static::assertTrue($this->listener->getMaxMemory() == 1024 * 25);
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

    public function testOnStopConditionCheckHandler()
    {
        $this->listener->setMaxMemory(1024 * 1024 * 1000);

        $result = $this->listener->onStopConditionCheck(new ProcessQueueEvent($this->worker, $this->queue));
        static::assertNull($result);

        $stateResult = $this->listener->onReportQueueState(new ProcessStateEvent($this->worker));
        static::assertStringContainsString(' memory usage', $stateResult->getState());


        $this->listener->setMaxMemory(1024);

        $result = $this->listener->onStopConditionCheck(new ProcessQueueEvent($this->worker, $this->queue));
        static::assertNotNull($result);
        static::assertInstanceOf(ExitWorkerLoopResult::class, $result);
        static::assertStringContainsString('memory threshold of 1kB exceeded (usage: ', $result->getReason());

        $stateResult = $this->listener->onReportQueueState(new ProcessStateEvent($this->worker));
        static::assertStringContainsString(' memory usage', $stateResult->getState());
    }
}
