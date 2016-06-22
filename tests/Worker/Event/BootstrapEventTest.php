<?php

namespace SlmQueueTest\Worker\Event;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Worker\Event\BootstrapEvent;

class WorkerEventTest extends TestCase
{
    protected $queue;
    protected $worker;
    protected $event;

    public function setUp()
    {
        $this->queue  = $this->getMock(\SlmQueue\Queue\QueueInterface::class);
        $this->worker = $this->getMock(\SlmQueue\Worker\WorkerInterface::class);
        $this->event  = new BootstrapEvent($this->worker, $this->queue);
    }

    public function testSetsWorkerAsTarget()
    {
        static::assertEquals($this->worker, $this->event->getTarget());
    }

    public function testWorkerEventGetsQueue()
    {
        static::assertEquals($this->queue, $this->event->getQueue());
    }

}
