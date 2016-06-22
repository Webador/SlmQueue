<?php

namespace SlmQueueTest\Worker;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Strategy\MaxRunsStrategy;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Event\BootstrapEvent;
use SlmQueue\Worker\Event\FinishEvent;
use SlmQueue\Worker\Event\ProcessQueueEvent;
use SlmQueue\Worker\Event\ProcessStateEvent;
use SlmQueue\Worker\Result\ExitWorkerLoopResult;
use SlmQueue\Worker\Result\ProcessStateResult;
use SlmQueueTest\Asset\SimpleWorker;
use Zend\EventManager\EventManager;
use Zend\EventManager\ResponseCollection;

class AbstractWorkerTest extends TestCase
{
    /** @var SimpleWorker */
    protected $worker;
    protected $queue;
    protected $job;
    protected $maxRuns;

    public function setUp()
    {
        $this->worker = new SimpleWorker;
        $this->queue  = $this->getMock(\SlmQueue\Queue\QueueInterface::class);
        $this->job    = $this->getMock(\SlmQueue\Job\JobInterface::class);

        // set max runs so our tests won't run forever
        $this->maxRuns = new MaxRunsStrategy;
        $this->maxRuns->setMaxRuns(1);
        $this->maxRuns->attach($this->worker->getEventManager());
    }

    public function testCorrectIdentifiersAreSetToEventManager()
    {
        /** @var EventManager $eventManager */
        $eventManager = $this->worker->getEventManager();

        static::assertContains(\SlmQueue\Worker\AbstractWorker::class, $eventManager->getIdentifiers());
        static::assertContains(\SlmQueueTest\Asset\SimpleWorker::class, $eventManager->getIdentifiers());
        static::assertContains(\SlmQueue\Worker\WorkerInterface::class, $eventManager->getIdentifiers());
    }

    public function testWorkerLoopEvents()
    {
        $eventManager = $this->getMock('Zend\EventManager\EventManager');
        $this->worker = new SimpleWorker($eventManager);

        // BootstrapEvent
        $eventManager->expects($this->at(0))->method('triggerEvent')->with(new BootstrapEvent($this->worker,
            $this->queue));

        // first ProcessQueueEvent with no exit
        $response = new ResponseCollection();
        $response->push(null);
        $eventManager->expects($this->at(1))->method('triggerEventUntil')->with(function () {
            return false;
        }, new ProcessQueueEvent($this->worker, $this->queue))->willReturn($response);

        // first ProcessQueueEvent with exit
        $response = new ResponseCollection();
        $response->push(ExitWorkerLoopResult::withReason('some exit reason'));
        $response->setStopped(true);
        $eventManager->expects($this->at(2))->method('triggerEventUntil')->with(function () {
            return true;
        }, new ProcessQueueEvent($this->worker, $this->queue))->willReturn($response);

        // FinishEvent
        $eventManager->expects($this->at(3))->method('triggerEvent')->with(new FinishEvent($this->worker,
            $this->queue));

        // ProcessStateEvent
        $response = new ResponseCollection();
        $response->push(ProcessStateResult::withState('some strategy state'));
        $response->push(ProcessStateResult::withState('another strategy state'));
        $eventManager->expects($this->at(4))->method('triggerEvent')->with(new ProcessStateEvent($this->worker))->willReturn($response);

        $result = $this->worker->processQueue($this->queue);

        static::assertEquals(["some strategy state", "another strategy state", "some exit reason"], $result);
    }

    public function testProcessQueueSetsOptionsOnProcessQueueEvent()
    {
        /** @var EventManager $eventManager */
        $eventManager = $this->worker->getEventManager();

        $options = ['foo' => 'bar'];

        $eventManager->attach(WorkerEventInterface::EVENT_PROCESS_QUEUE, function (ProcessQueueEvent $e) use ($options) {
            static::assertEquals($options, $e->getOptions());
        });

        $this->worker->processQueue($this->queue, $options);
    }
}
