<?php

namespace SlmQueueTest\Strategy;

use PHPUnit\Framework\TestCase;
use SlmQueue\Strategy\MaxPollingFrequencyStrategy;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Event\ProcessQueueEvent;
use SlmQueueTest\Asset\SimpleWorker;

class MaxPollingFrequencyStrategyTest extends TestCase
{
    protected $queue;
    protected $worker;
    /** @var MaxPollingFrequencyStrategy */
    protected $listener;

    public function setUp(): void
    {
        $this->queue    = $this->createMock(\SlmQueue\Queue\QueueInterface::class);
        $this->worker   = new SimpleWorker();
        $this->listener = new MaxPollingFrequencyStrategy();
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        static::assertInstanceOf(\SlmQueue\Strategy\AbstractStrategy::class, $this->listener);
    }

    public function testMaxPollingFrequencySetter()
    {
        $this->listener->setMaxFrequency(100);

        static::assertEquals(100, $this->listener->getMaxFrequency());
    }

    public function testListensToCorrectEventAtCorrectPriority()
    {
        $evm      = $this->createMock(\Laminas\EventManager\EventManagerInterface::class);
        $priority = 1;

        $evm->expects($this->at(0))->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_QUEUE, [$this->listener, 'onQueueProcessFinish'], 1000);

        $this->listener->attach($evm, $priority);
    }

    public function testDelayWhenFrequencyIsSet()
    {
        $this->listener->setMaxFrequency(1);

        $startTime = microtime(true);
        $this->listener->onQueueProcessFinish(new ProcessQueueEvent($this->worker, $this->queue));
        $this->listener->onQueueProcessFinish(new ProcessQueueEvent($this->worker, $this->queue));
        $endTime = microtime(true);
        $delay   = round($endTime - $startTime);

        static::assertEquals(1, $delay);
    }
}
