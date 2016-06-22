<?php

namespace SlmQueueTest\Worker\Event;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Worker\Event\ProcessQueueEvent;

class ProcessQueueEventTest extends TestCase
{
    protected $queue;
    protected $worker;
    protected $event;
    protected $options;

    public function setUp()
    {
        $this->queue   = $this->getMock(\SlmQueue\Queue\QueueInterface::class);
        $this->worker  = $this->getMock(\SlmQueue\Worker\WorkerInterface::class);
        $this->options = ['foo' => 'bar'];
        $this->event   = new ProcessQueueEvent($this->worker, $this->queue, $this->options);
    }

    public function testSetsWorkerAsTarget()
    {
        static::assertEquals($this->worker, $this->event->getWorker());
    }

    public function testGetsQueue()
    {
        static::assertEquals($this->queue, $this->event->getQueue());
    }

    public function testGetsOptions()
    {
        static::assertEquals($this->options, $this->event->getOptions());
    }
}
