<?php

namespace SlmQueueTest\Listener\Strategy;

use PHPUnit_Framework_TestCase;
use SlmQueue\Strategy\ProcessQueueStrategy;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Event\ProcessJobEvent;
use SlmQueue\Worker\Event\ProcessQueueEvent;
use SlmQueue\Worker\Result\ExitWorkerLoopResult;
use SlmQueueTest\Asset\SimpleJob;
use SlmQueueTest\Asset\SimpleWorker;

class ProcessQueueStrategyTest extends PHPUnit_Framework_TestCase
{
    protected $queue;
    protected $worker;
    /** @var ProcessQueueStrategy */
    protected $listener;

    public function setUp()
    {
        $this->queue    = $this->getMock(\SlmQueue\Queue\QueueInterface::class);
        $this->worker   = new SimpleWorker();
        $this->listener = new ProcessQueueStrategy();
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        static::assertInstanceOf(\SlmQueue\Strategy\AbstractStrategy::class, $this->listener);
    }

    public function testListensToCorrectEventAtCorrectPriority()
    {
        $evm      = $this->getMock(\Zend\EventManager\EventManagerInterface::class);
        $priority = 1;

        $evm->expects($this->at(0))
            ->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_QUEUE, [$this->listener, 'onJobPop'], $priority);
        $evm->expects($this->at(1))
            ->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_JOB, [$this->listener, 'onJobProcess'], $priority);

        $this->listener->attach($evm, $priority);
    }

    public function testJobPopWithEmptyQueueTriggersIdleAndNoExitResultIsReturned()
    {
        $popOptions = [];
        $this->queue->expects($this->at(0))
            ->method('pop')
            ->with($popOptions)
            ->willReturn(null);

        $event = new ProcessQueueEvent($this->worker, $this->queue, $popOptions);

        $triggeredIdle = false;
        $this->worker->getEventManager()->attach(WorkerEventInterface::EVENT_PROCESS_IDLE,
            function ($e) use (&$triggeredIdle) {
                $triggeredIdle = true;
            });

        $result = $this->listener->onJobPop($event);

        static::assertNull($result);
        static::assertTrue($triggeredIdle);
        static::assertTrue($event->propagationIsStopped(), "EventPropagation should be stopped");
    }

    public function testJobPopWithEmptyQueueTriggersIdleAndExitResultIsReturned()
    {
        $popOptions = [];
        $this->queue->expects($this->at(0))
            ->method('pop')
            ->with($popOptions)
            ->willReturn(null);

        $event = new ProcessQueueEvent($this->worker, $this->queue, $popOptions);

        $triggeredIdle = false;
        $this->worker->getEventManager()->attach(WorkerEventInterface::EVENT_PROCESS_IDLE,
            function ($e) use (&$triggeredIdle) {
                $triggeredIdle = true;

                return ExitWorkerLoopResult::withReason('some reason');
            });

        $result = $this->listener->onJobPop($event);

        static::assertInstanceOf(ExitWorkerLoopResult::class, $result);
        static::assertTrue($triggeredIdle);
        static::assertTrue($event->propagationIsStopped(), "EventPropagation should be stopped");
    }

    public function testJobPopWithJobTriggersProcessJobEvent()
    {
        $job        = new SimpleJob();
        $popOptions = [];
        $this->queue->expects($this->at(0))
            ->method('pop')
            ->with($popOptions)
            ->willReturn($job);

        $event = new ProcessQueueEvent($this->worker, $this->queue, $popOptions);

        $triggeredProcessJobEvent = false;
        $this->worker->getEventManager()->attach(WorkerEventInterface::EVENT_PROCESS_JOB,
            function ($e) use (&$triggeredProcessJobEvent) {
                $triggeredProcessJobEvent = true;
            });

        $result = $this->listener->onJobPop($event);

        static::assertNull($result);
        static::assertTrue($triggeredProcessJobEvent);
        static::assertFalse($event->propagationIsStopped(), "EventPropagation should not be stopped");
    }

    public function testOnJobProcess()
    {
        $job   = new SimpleJob();
        $event = new ProcessJobEvent($job, $this->worker, $this->queue);

        $result = $this->listener->onJobProcess($event);

        static::assertNull($result);
        static::assertSame('result', $event->getResult());
        static::assertEquals($job, $event->getJob());
        static::assertSame('bar', $event->getJob()->getMetadata('foo'));
    }

}
