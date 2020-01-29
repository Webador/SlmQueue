<?php

namespace SlmQueueTest\Worker\Event;

use PHPUnit\Framework\TestCase;
use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\Event\ProcessJobEvent;
use SlmQueue\Worker\WorkerInterface;

class ProcessJobEventTest extends TestCase
{
    protected $queue;
    protected $worker;
    protected $job;
    protected $event;

    public function setUp(): void
    {
        $this->queue = $this->createMock(QueueInterface::class);
        $this->worker = $this->createMock(WorkerInterface::class);
        $this->job = $this->createMock(JobInterface::class);
        $this->event = new ProcessJobEvent($this->job, $this->worker, $this->queue);
    }

    public function testSetsWorkerAsTarget()
    {
        static::assertEquals($this->worker, $this->event->getWorker());
    }

    public function testGetsQueue()
    {
        static::assertEquals($this->queue, $this->event->getQueue());
    }

    public function testGetsJob()
    {
        static::assertEquals($this->job, $this->event->getJob());
    }

    public function testDefaultResultStatusIsUnknown()
    {
        static::assertEquals(ProcessJobEvent::JOB_STATUS_UNKNOWN, $this->event->getResult());
    }
}
