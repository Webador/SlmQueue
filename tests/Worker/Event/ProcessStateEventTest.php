<?php

namespace SlmQueueTest\Worker\Event;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Worker\Event\ProcessStateEvent;

class ProcessStateEventTest extends TestCase
{
    protected $worker;
    protected $event;
    protected $queue;

    public function setUp()
    {
        $this->queue  = $this->getMock(\SlmQueue\Queue\QueueInterface::class);
        $this->worker = $this->getMock(\SlmQueue\Worker\WorkerInterface::class);
        $this->event  = new ProcessStateEvent($this->worker, $this->queue);
    }

    public function testSetsWorkerAsTarget()
    {
        static::assertEquals($this->worker, $this->event->getTarget());
    }
}
