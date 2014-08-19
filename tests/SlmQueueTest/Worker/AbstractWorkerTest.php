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

    public function testWorkerPopsFromQueue()
    {
        $this->queue->expects($this->once())
                    ->method('pop')
                    ->will($this->returnValue($this->job));

        $this->worker->processQueue($this->queue);
    }

    public function testWorkerExecutesJob()
    {
        $this->queue->expects($this->once())
                    ->method('pop')
                    ->will($this->returnValue($this->job));

        $this->job->expects($this->once())
                  ->method('execute');

        $this->worker->processQueue($this->queue);
    }

    public function testWorkerCountsRuns()
    {
        $this->maxRuns->setMaxRuns(2);

        $this->queue->expects($this->exactly(2))
                    ->method('pop')
                    ->will($this->returnValue($this->job));

        $this->worker->processQueue($this->queue);
    }

    public function testWorkerReturnsArray()
    {
        $this->queue->expects($this->once())
                    ->method('pop')
                    ->will($this->returnValue($this->job));

        $this->assertTrue(is_array($this->worker->processQueue($this->queue)));
    }

    public function testWorkerContainsMessages()
    {
        $this->queue->expects($this->once())
                    ->method('pop')
                    ->will($this->returnValue($this->job));

        $this->assertContains('maximum of 1 jobs processed', $this->worker->processQueue($this->queue));
    }

    public function testWorkerSkipsVoidValuesFromQueue()
    {
        $i   = 0;
        $job = $this->job;
        $callback = function () use (&$i, $job) {
            // We return the job on the 4th call
            if ($i === 3) {
                return $job;
            }

            $i++;
            return null;
        };

        $this->maxRuns->setMaxRuns(1);
        $this->queue->expects($this->exactly(4))
                    ->method('pop')
                    ->will($this->returnCallback($callback));

        $this->worker->processQueue($this->queue);
    }

    public function testCorrectIdentifiersAreSetToEventManager()
    {
        $eventManager = $this->worker->getEventManager();

        $this->assertContains('SlmQueue\Worker\WorkerInterface', $eventManager->getIdentifiers());
        $this->assertContains('SlmQueueTest\Asset\SimpleWorker', $eventManager->getIdentifiers());
    }

    public function testEventManagerTriggersEvents()
    {
        /**
         * The stop condition is now a listener on the event manager, this
         * makes it really hard to test this thing. We cannot use attach here
         * as the trigger will not call the listeners (the "trigger" is mocked),
         * however if we do not mock the EVM, we cannot assert that the triggers
         * are going...
         */
        $this->markTestSkipped('TODO: This test should still be fixed');

        $eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');
        $this->worker = new SimpleWorker($eventManager);

        $this->queue->expects($this->once())
                    ->method('pop')
                    ->will($this->returnValue($this->job));

        // Trigger will be called 3: one for bootstrap, process and finish

        $eventManager->expects($this->exactly(3))
                     ->method('trigger');

        $eventManager->expects($this->at(0))
                     ->method('trigger')
                     ->with($this->equalTo(WorkerEvent::EVENT_BOOTSTRAP));

        $eventManager->expects($this->at(1))
                     ->method('trigger')
                     ->with($this->equalTo(WorkerEvent::EVENT_PROCESS));

        $eventManager->expects($this->at(2))
                     ->method('trigger')
                     ->with($this->equalTo(WorkerEvent::EVENT_FINISH));

        $this->worker->processQueue($this->queue);
    }

    public function testWorkerSetsJobStatusInEventClass()
    {
        $eventManager = new EventManager;
        $this->worker = new SimpleWorker($eventManager);
        $this->worker->getEventManager()->attach($this->maxRuns);

        $this->job->expects($this->once())
                  ->method('execute')
                  ->will($this->returnValue(WorkerEvent::JOB_STATUS_SUCCESS));

        $this->queue->expects($this->once())
                    ->method('pop')
                    ->will($this->returnValue($this->job));

        $self = $this;
        $eventManager->attach(WorkerEvent::EVENT_PROCESS, function ($e) use ($self) {
            $self->assertEquals(WorkerEvent::JOB_STATUS_SUCCESS, $e->getResult());
        }, -100);

        $this->worker->processQueue($this->queue);
    }

}
