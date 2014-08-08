<?php

namespace SlmQueueTest\Worker;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Worker\WorkerEvent;
use SlmQueueTest\Asset\SimpleJob;

class WorkerEventTest extends TestCase
{
    protected $queue;
    protected $worker;

    public function setUp()
    {
        $this->queue  = $this->getMock('SlmQueue\Queue\QueueInterface');
        $this->worker = $this->getMock('SlmQueue\Worker\WorkerInterface');
    }

    public function testWorkerEventSetsWorkerAsTarget()
    {
        $event = new WorkerEvent($this->worker, $this->queue);

        $this->assertEquals($this->worker, $event->getTarget());
    }

    public function testWorkerEventHoldsStateForQueue()
    {
        $event = new WorkerEvent($this->worker, $this->queue);

        $this->assertEquals($this->queue, $event->getQueue());
    }

    public function getWorkerEventHoldsStateForJob()
    {
        $event = new WorkerEvent($this->worker, $this->queue);

        $job = new SimpleJob;
        $event->setJob($job);

        $this->assertEquals($job, $event->getJob());
    }
}
