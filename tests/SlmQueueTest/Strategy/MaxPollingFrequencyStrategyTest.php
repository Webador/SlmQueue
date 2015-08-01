<?php

namespace SlmQueueTest\Strategy;

use PHPUnit_Framework_TestCase;
use SlmQueue\Strategy\MaxPollingFrequencyStrategy;
use SlmQueue\Worker\WorkerEvent;
use SlmQueueTest\Asset\SimpleJob;

class MaxPollingFrequencyStrategyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MaxPollingFrequencyStrategy
     */
    protected $listener;

    /**
     * @var WorkerEvent
     */
    protected $event;

    public function setUp()
    {
        $queue = $this->getMockBuilder('SlmQueue\Queue\AbstractQueue')
            ->disableOriginalConstructor()
            ->getMock();
        $worker = $this->getMock('SlmQueue\Worker\WorkerInterface');

        $ev    = new WorkerEvent($worker, $queue);
        $job   = new SimpleJob();

        $ev->setJob($job);

        $this->listener = new MaxPollingFrequencyStrategy();
        $this->event    = $ev;
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        $this->assertInstanceOf('SlmQueue\Strategy\AbstractStrategy', $this->listener);
    }

    public function testMaxPollingFrequencySetter()
    {
        $this->listener->setMaxFrequency(100);

        $this->assertTrue($this->listener->getMaxFrequency() == 100);
    }

    public function testListensToCorrectEvents()
    {
        $evm = $this->getMock('Zend\EventManager\EventManagerInterface');

        $evm->expects($this->at(0))->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS_QUEUE, [$this->listener, 'onQueueProcessFinish']);

        $this->listener->attach($evm);
    }

    public function testDelayWhenFrequencyIsSet()
    {
        $this->listener->setMaxFrequency(1);

        $startTime = microtime(true);
        $this->listener->onQueueProcessFinish($this->event);
        $this->listener->onQueueProcessFinish($this->event);
        $endTime = microtime(true);
        $delay = round($endTime - $startTime);

        $this->assertEquals(1, $delay);
    }
}
