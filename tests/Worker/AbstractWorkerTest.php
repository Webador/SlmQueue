<?php

namespace SlmQueueTest\Worker;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Worker\WorkerEvent;
use SlmQueue\Strategy\MaxRunsStrategy;
use SlmQueueTest\Asset\SimpleWorker;
use Zend\EventManager\EventManager;

class AbstractWorkerTest extends TestCase
{
    protected $options, $worker, $queue, $job;

    public function setUp()
    {
        $this->worker  = new SimpleWorker;
        $this->queue   = $this->getMock('SlmQueue\Queue\QueueInterface');
        $this->job     = $this->getMock('SlmQueue\Job\JobInterface');

        // set max runs so our tests won't run forever
        $this->maxRuns = new MaxRunsStrategy;
        $this->maxRuns->setMaxRuns(1);
        $this->maxRuns->attach($this->worker->getEventManager());
    }

    public function testCorrectIdentifiersAreSetToEventManager()
    {
        $eventManager = $this->worker->getEventManager();

        $this->assertContains('SlmQueue\Worker\WorkerInterface', $eventManager->getIdentifiers());
        $this->assertContains('SlmQueueTest\Asset\SimpleWorker', $eventManager->getIdentifiers());
    }

    /**
     * @dataProvider providerWorkerLoopEvents
     */
    public function testWorkerLoopEvents($exitedBy, $exitAfter, $expectedCalledEvents)
    {
        $this->worker  = new SimpleWorker();

        /** @var EventManager $eventManager */
        $eventManager = $this->worker->getEventManager();

        $this->exitedBy     = $exitedBy;
        $this->exitAfter    = $exitAfter;
        $this->actualCalled = [];

        $eventManager->attach(WorkerEvent::EVENT_BOOTSTRAP, [$this, 'callbackWorkerLoopEvents']);
        $eventManager->attach(WorkerEvent::EVENT_FINISH, [$this, 'callbackWorkerLoopEvents']);
        $eventManager->attach(WorkerEvent::EVENT_PROCESS_IDLE, [$this, 'callbackWorkerLoopEvents']);
        $eventManager->attach(WorkerEvent::EVENT_PROCESS_QUEUE, [$this, 'callbackWorkerLoopEvents']);
        $eventManager->attach(WorkerEvent::EVENT_PROCESS_STATE, [$this, 'callbackWorkerLoopEvents']);

        $this->worker->processQueue($this->queue);

        $this->assertEquals($expectedCalledEvents, $this->actualCalled);
    }

    public function providerWorkerLoopEvents()
    {
        return [
            [WorkerEvent::EVENT_BOOTSTRAP, 1, [
                WorkerEvent::EVENT_BOOTSTRAP     => 1,
                WorkerEvent::EVENT_FINISH        => 1,
                WorkerEvent::EVENT_PROCESS_STATE => 1]
            ],
            [WorkerEvent::EVENT_PROCESS_QUEUE, 10, [
                WorkerEvent::EVENT_BOOTSTRAP     => 1,
                WorkerEvent::EVENT_PROCESS_QUEUE => 10,
                WorkerEvent::EVENT_PROCESS_IDLE  => 5,
                WorkerEvent::EVENT_FINISH        => 1,
                WorkerEvent::EVENT_PROCESS_STATE => 1]
            ]
        ];
    }

    /**
     * Callback facilitating the worker loop
     *
     * It simulates a process queue strategy. And triggers an idle event on every uneven invokation of the PROCESS event
     *
     * @param WorkerEvent $e
     */
    public function callbackWorkerLoopEvents(WorkerEvent $e)
    {
        if (!isset($this->actualCalled[$e->getName()])) {
            $this->actualCalled[$e->getName()] = 1;
        } else {
            $this->actualCalled[$e->getName()]++;
        }

        // mark for exit when event is due
        if ($e->getName() == $this->exitedBy && $this->actualCalled[$e->getName()] >= $this->exitAfter) {
            $e->exitWorkerLoop();
        }

        // simulate process queue strategy, trigger idle event on every uneven call
        if ($e->getName() == WorkerEvent::EVENT_PROCESS_QUEUE) {
            if (!($this->actualCalled[WorkerEvent::EVENT_PROCESS_QUEUE] % 2)) {
                $e->setName(WorkerEvent::EVENT_PROCESS_IDLE);
                $e->getTarget()->getEventManager()->triggerEvent($e);

                // make sure the event doesn't propagate or it will still process
                $e->stopPropagation();
                return $e;
            }
        }

        return $e;
    }

//    public function testProcessQueueSetOptionsOnWorkerEvent()
//    {
//        /** @var EventManager $eventManager */
//        $eventManager = $this->worker->getEventManager();
//
//        $eventManager->attach(WorkerEvent::EVENT_PROCESS_QUEUE, [$this, 'callbackProcessQueueSetOptionsOnWorkerEvent']);
//
//        $options = ['foo' => 'bar'];
//
//        $this->worker->processQueue($this->queue, $options);
//
//        $this->assertEquals($this->eventOptions, $options);
//    }
//
//    /**
//     * Callback facilitating the worker loop
//     *
//     * @param WorkerEvent $e
//     */
//    public function callbackProcessQueueSetOptionsOnWorkerEvent(WorkerEvent $e)
//    {
//        $e->exitWorkerLoop();
//
//        $this->eventOptions = $e->getOptions();
//    }
}
