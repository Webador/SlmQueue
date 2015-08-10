<?php

namespace SlmQueueTest\Listener\Strategy;

use PHPUnit_Framework_TestCase;
use SlmQueue\Strategy\ProcessQueueStrategy;
use SlmQueue\Worker\WorkerEvent;
use SlmQueueTest\Asset\SimpleJob;
use SlmQueueTest\Asset\SimpleWorker;

class ProcessQueueStrategyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ProcessQueueStrategy
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

        $worker = new SimpleWorker();

        $event    = new WorkerEvent($worker, $queue);
        $this->job   = new SimpleJob();
        $event->setOptions(['foo' => 'bar']);
        $event->setJob($this->job);

        $this->listener = new ProcessQueueStrategy();
        $this->event    = $event;
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        $this->assertInstanceOf('SlmQueue\Strategy\AbstractStrategy', $this->listener);
    }

    public function testListensToCorrectEvents()
    {
        $evm = $this->getMock('Zend\EventManager\EventManagerInterface');

        $priority = 1;

        $evm->expects($this->at(0))
            ->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS_QUEUE, [$this->listener, 'onJobPop'], $priority);
        $evm->expects($this->at(1))
            ->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS_JOB, [$this->listener, 'onJobProcess'], $priority);

        $this->listener->attach($evm, $priority);
    }

    public function testOnJobPopHandler()
    {
        $this->listener->onJobPop($this->event);
        $this->assertFalse($this->event->shouldExitWorkerLoop());
    }

    public function testOnJobPopPopsFromQueueWithOptions()
    {
        $this->event->getQueue()
            ->expects($this->once())
            ->method('pop')
            ->with(['foo' => 'bar'])
            ->will($this->returnValue($this->job));

        $called = false;

        $this->event->getTarget()->getEventManager()->attach(
            WorkerEvent::EVENT_PROCESS_JOB,
            function(WorkerEvent $e) use (&$called) {
                $called = true;
            }
        );

        $this->listener->onJobPop($this->event);

        $this->assertTrue($called);
        $this->assertSame($this->job, $this->event->getJob());
    }

    public function testOnJobPopPopsTriggersIdleAndStopPropagation()
    {
        $this->event->getQueue()
            ->expects($this->once())
            ->method('pop')
            ->will($this->returnValue(null));

        $called = false;
        $this->event->getTarget()->getEventManager()->attach(
            WorkerEvent::EVENT_PROCESS_IDLE,
            function(WorkerEvent $e) use (&$called) {
                $called = true;
            }
        );

        $this->listener->onJobPop($this->event);

        $this->assertTrue($called);
        $this->assertNull($this->event->getJob());
        $this->assertEquals(WorkerEvent::JOB_STATUS_UNKNOWN, $this->event->getResult());
        $this->assertTrue($this->event->propagationIsStopped());
    }

    public function testOnJobProcessHandlerEventGetsJobResult()
    {
        $this->listener->onJobProcess($this->event);
        $this->assertTrue($this->event->getResult() == 'result');
    }

}
