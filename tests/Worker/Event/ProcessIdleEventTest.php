<?php

namespace SlmQueueTest\Worker\Event;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Worker\Event\ProcessIdleEvent;

class ProcessIdleEventTest extends TestCase
{
    protected $queue;
    protected $worker;
    protected $event;

    public function setUp()
    {
        $this->queue  = $this->getMock(\SlmQueue\Queue\QueueInterface::class);
        $this->worker = $this->getMock(\SlmQueue\Worker\WorkerInterface::class);
        $this->event  = new ProcessIdleEvent($this->worker, $this->queue);
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
