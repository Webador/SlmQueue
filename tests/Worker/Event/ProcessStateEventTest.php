<?php

namespace SlmQueueTest\Worker\Event;

use PHPUnit\Framework\TestCase as TestCase;
use SlmQueue\Worker\Event\ProcessStateEvent;

class ProcessStateEventTest extends TestCase
{
    protected $worker;
    protected $event;
    protected $queue;

    public function setUp(): void
    {
        $this->queue  = $this->createMock(\SlmQueue\Queue\QueueInterface::class);
        $this->worker = $this->createMock(\SlmQueue\Worker\WorkerInterface::class);
        $this->event  = new ProcessStateEvent($this->worker, $this->queue);
    }

    public function testSetsWorkerAsTarget()
    {
        static::assertEquals($this->worker, $this->event->getWorker());
    }
}
