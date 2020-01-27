<?php

namespace SlmQueueTest\Worker\Event;

use PHPUnit\Framework\TestCase as TestCase;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\Event\ProcessIdleEvent;
use SlmQueue\Worker\WorkerInterface;

class ProcessIdleEventTest extends TestCase
{
    protected $queue;
    protected $worker;
    protected $event;

    public function setUp(): void
    {
        $this->queue = $this->createMock(QueueInterface::class);
        $this->worker = $this->createMock(WorkerInterface::class);
        $this->event = new ProcessIdleEvent($this->worker, $this->queue);
    }

    public function testSetsWorkerAsTarget()
    {
        static::assertEquals($this->worker, $this->event->getWorker());
    }

    public function testGetsQueue()
    {
        static::assertEquals($this->queue, $this->event->getQueue());
    }
}
