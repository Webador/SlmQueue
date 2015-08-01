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

    /**
     * Ensure that calling setJob will reset the event result to JOB_STATUS_UNKNOWN
     */
    public function testSetJobResetsResult()
    {
        $event = new WorkerEvent($this->worker, $this->queue);
        $event->setResult(WorkerEvent::JOB_STATUS_SUCCESS);

        $job = new SimpleJob;
        $event->setJob($job);

        $this->assertEquals(WorkerEvent::JOB_STATUS_UNKNOWN, $event->getResult());
    }

    /**
     * Ensure that an existing (previously processed) job can be removed from the event
     */
    public function testEventJobCanBeCleared()
    {
        $event = new WorkerEvent($this->worker, $this->queue);
        $job = new SimpleJob;

        $event->setJob($job);
        $this->assertNotNull($event->getJob());

        $event->setJob(null);
        $this->assertNull($event->getJob());
    }
}
