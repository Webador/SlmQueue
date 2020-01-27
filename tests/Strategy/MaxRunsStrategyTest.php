<?php

namespace SlmQueueTest\Listener\Strategy;

use PHPUnit_Framework_TestCase;
use SlmQueue\Strategy\MaxRunsStrategy;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Event\ProcessQueueEvent;
use SlmQueue\Worker\Event\ProcessStateEvent;
use SlmQueue\Worker\Result\ExitWorkerLoopResult;
use SlmQueueTest\Asset\SimpleJob;
use SlmQueueTest\Asset\SimpleWorker;

class MaxRunsStrategyTest extends PHPUnit_Framework_TestCase
{
    protected $queue;
    protected $worker;
    /** @var MaxRunsStrategy */
    protected $listener;

    public function setUp()
    {
        $this->queue    = $this->getMock(\SlmQueue\Queue\QueueInterface::class);
        $this->worker   = new SimpleWorker();
        $this->listener = new MaxRunsStrategy();
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        static::assertInstanceOf(\SlmQueue\Strategy\AbstractStrategy::class, $this->listener);
    }

    public function testMaxRunsDefault()
    {
        static::assertEquals(0, $this->listener->getMaxRuns());
    }

    public function testMaxRunsSetter()
    {
        $this->listener->setMaxRuns(2);

        static::assertEquals(2, $this->listener->getMaxRuns());
    }

    public function testListensToCorrectEventAtCorrectPriority()
    {
        $evm = $this->getMock(\Laminas\EventManager\EventManagerInterface::class);
        $priority = 1;

        $evm->expects($this->at(0))->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_QUEUE, [$this->listener, 'onStopConditionCheck'], -1000);
        $evm->expects($this->at(1))->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_STATE, [$this->listener, 'onReportQueueState'], 1);

        $this->listener->attach($evm, $priority);
    }

    public function testOnStopConditionCheckHandler()
    {
        $this->listener->setMaxRuns(3);

        $result = $this->listener->onStopConditionCheck(new ProcessQueueEvent($this->worker, $this->queue));
        static::assertNull($result);

        $stateResult = $this->listener->onReportQueueState(new ProcessStateEvent($this->worker));
        static::assertContains('1 jobs processed', $stateResult->getState());

        $result = $this->listener->onStopConditionCheck(new ProcessQueueEvent($this->worker, $this->queue));
        static::assertNull($result);

        $stateResult = $this->listener->onReportQueueState(new ProcessStateEvent($this->worker));
        static::assertContains('2 jobs processed', $stateResult->getState());

        $result = $this->listener->onStopConditionCheck(new ProcessQueueEvent($this->worker, $this->queue));
        static::assertNotNull($result);
        static::assertInstanceOf(ExitWorkerLoopResult::class, $result);
        static::assertContains('maximum of 3 jobs processed', $result->getReason());

        $stateResult = $this->listener->onReportQueueState(new ProcessStateEvent($this->worker));
        static::assertContains('3 jobs processed', $stateResult->getState());
    }
}
