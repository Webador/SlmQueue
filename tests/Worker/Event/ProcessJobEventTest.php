<?php

namespace SlmQueueTest\Worker\Event;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Worker\Event\ProcessJobEvent;

class ProcessJobEventTest extends TestCase
{
    protected $queue;
    protected $worker;
    protected $job;
    protected $event;

    public function setUp()
    {
        $this->queue  = $this->getMock(\SlmQueue\Queue\QueueInterface::class);
        $this->worker = $this->getMock(\SlmQueue\Worker\WorkerInterface::class);
        $this->job    = $this->getMock(\SlmQueue\Job\JobInterface::class);
        $this->event  = new ProcessJobEvent($this->job, $this->worker, $this->queue);
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
