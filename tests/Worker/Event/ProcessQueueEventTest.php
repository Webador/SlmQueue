<?php

namespace SlmQueueTest\Worker\Event;

use PHPUnit\Framework\TestCase;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\Event\ProcessQueueEvent;
use SlmQueue\Worker\WorkerInterface;

class ProcessQueueEventTest extends TestCase
{
    protected $queue;
    protected $worker;
    protected $event;
    protected $options;

    public function setUp(): void
    {
        $this->queue = $this->createMock(QueueInterface::class);
        $this->worker = $this->createMock(WorkerInterface::class);
        $this->options = ['foo' => 'bar'];
        $this->event = new ProcessQueueEvent($this->worker, $this->queue, $this->options);
    }

    public function testSetsWorkerAsTarget(): void
    {
        static::assertEquals($this->worker, $this->event->getWorker());
    }

    public function testGetsQueue(): void
    {
        static::assertEquals($this->queue, $this->event->getQueue());
    }

    public function testGetsOptions(): void
    {
        static::assertEquals($this->options, $this->event->getOptions());
    }
}
