<?php

namespace SlmQueueTest\Worker;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Strategy\InterruptStrategy;
use SlmQueue\Strategy\ProcessQueueStrategy;
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
        $this->worker->getEventManager()->attach($this->maxRuns);
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
        $this->actualCalled = array();

        $eventManager->attach(WorkerEvent::EVENT_BOOTSTRAP, array($this, 'callbackWorkerLoopEvents'));
        $eventManager->attach(WorkerEvent::EVENT_FINISH, array($this, 'callbackWorkerLoopEvents'));
        $eventManager->attach(WorkerEvent::EVENT_PROCESS_IDLE, array($this, 'callbackWorkerLoopEvents'));
        $eventManager->attach(WorkerEvent::EVENT_PROCESS, array($this, 'callbackWorkerLoopEvents'));
        $eventManager->attach(WorkerEvent::EVENT_PROCESS_STATE, array($this, 'callbackWorkerLoopEvents'));

        $this->worker->processQueue($this->queue);

        $this->assertEquals($expectedCalledEvents, $this->actualCalled);
    }

    public function providerWorkerLoopEvents() {
        return array(
            array(WorkerEvent::EVENT_BOOTSTRAP, 1, array('bootstrap' => 1, 'finish' => 1, 'state' => 1)),
            array(WorkerEvent::EVENT_PROCESS, 10, array('bootstrap' => 1, 'process' => 10, 'idle' => 5, 'finish' => 1, 'state' => 1))
        );
    }

    /**
     * Callback facilitating the worker loop
     *
     * It simulates a process queue strategy. And triggers an idle event on every uneven invokation of the PROCESS event
     *
     * @param WorkerEvent $e
     */
    public function callbackWorkerLoopEvents(WorkerEvent $e) {
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
        if ($e->getName() == WorkerEvent::EVENT_PROCESS) {
            if (!($this->actualCalled[WorkerEvent::EVENT_PROCESS] % 2)) {
                $e->getTarget()->getEventManager()->trigger(WorkerEvent::EVENT_PROCESS_IDLE, $e);
                $e->stopPropagation();

                return;
            }
        }
    }

    public function testProcessQueueSetOptionsOnWorkerEvent() {
        /** @var EventManager $eventManager */
        $eventManager = $this->worker->getEventManager();

        $eventManager->attach(WorkerEvent::EVENT_PROCESS, array($this, 'callbackProcessQueueSetOptionsOnWorkerEvent'));

        $options = array('foo' => 'bar');

        $this->worker->processQueue($this->queue, $options);

        $this->assertEquals($this->eventOptions, $options);
    }

    /**
     * Callback facilitating the worker loop
     *
     * @param WorkerEvent $e
     */
    public function callbackProcessQueueSetOptionsOnWorkerEvent(WorkerEvent $e) {
        $e->exitWorkerLoop();

        $this->eventOptions = $e->getOptions();
    }
}
