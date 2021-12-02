<?php

namespace SlmQueueTest\Worker\Event;

use PHPUnit\Framework\TestCase;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\Event\ProcessStateEvent;
use SlmQueue\Worker\WorkerInterface;

class ProcessStateEventTest extends TestCase
{
    protected $worker;
    protected $event;
    protected $queue;

    public function setUp(): void
    {
        $this->queue = $this->createMock(QueueInterface::class);
        $this->worker = $this->createMock(WorkerInterface::class);
        $this->event = new ProcessStateEvent($this->worker, $this->queue);
    }

    public function testSetsWorkerAsTarget(): void
    {
        static::assertEquals($this->worker, $this->event->getWorker());
    }
}
