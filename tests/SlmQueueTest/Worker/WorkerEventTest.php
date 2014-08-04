<?php
 
namespace SlmQueueTest\Worker;
 
use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Worker\WorkerEvent;
use SlmQueueTest\Asset\SimpleJob;

class WorkerEventTest extends TestCase
{
    protected $queue;

    public function setUp()
    {
        $this->queue = $this->getMock('SlmQueue\Queue\QueueInterface');
    }
    public function testWorkerEventHoldsStateForQueue()
    {
        $event = new WorkerEvent($this->queue);

        $this->assertEquals($this->queue, $event->getQueue());
    }

    public function getWorkerEventHoldsStateForJob()
    {
        $event = new WorkerEvent($this->queue);

        $job = new SimpleJob;
        $event->setJob($job);

        $this->assertEquals($job, $event->getJob());
    }
}
