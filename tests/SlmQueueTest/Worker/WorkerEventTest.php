<?php
 
namespace SlmQueueTest\Worker;
 
use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Worker\WorkerEvent;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueueTest\Asset\SimpleQueue;
use SlmQueueTest\Asset\SimpleJob;
use Zend\ServiceManager\Config;

class WorkerEventTest extends TestCase
{
    protected $queue;

    public function setUp()
    {
        $queuePluginManager = new QueuePluginManager(new Config(array(
            'factories' => array(
                'simpleQueue' => 'SlmQueueTest\Asset\SimpleQueueFactory'
            )
        )));

        $this->queue = $queuePluginManager->get('simpleQueue');
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
