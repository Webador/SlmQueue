<?php

namespace SlmQueueTest\Worker;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Worker\WorkerEvent;
use SlmQueueTest\Asset\SimpleWorker;

class AbstractWorkerTest extends TestCase
{
    protected $worker, $plugins, $options, $queue, $job;

    public function setUp()
    {
        $queueName = 'foo';
        $options   = array();
        $options['max_runs'] = 1;
        $options['max_memory'] = 1024*1024*1024;

        $plugins = $this->getMock('SlmQueue\Queue\QueuePluginManager', array('get'));
        $worker  = new SimpleWorker($plugins, $options);

        $queue   = $this->getMock('SlmQueue\Queue\QueueInterface');
        $plugins->expects($this->any())
                ->method('get')
                ->with($queueName)
                ->will($this->returnValue($queue));

        $job = $this->getMock('SlmQueue\Job\JobInterface');

        $this->worker  = $worker;
        $this->plugins = $plugins;
        $this->options = $options;
        $this->queue   = $queue;
        $this->job     = $job;
    }
    public function testWorkerPopsFromQueue()
    {
        $this->markTestSkipped('This test has been broken.');

        $this->queue->expects($this->once())
                    ->method('pop')
                    ->will($this->returnValue($this->job));

        $this->worker->processQueue('foo');
    }

    public function testWorkerExecutesJob()
    {
        $this->markTestSkipped('This test has been broken.');

        $this->queue->expects($this->once())
                    ->method('pop')
                    ->will($this->returnValue($this->job));

        $this->job->expects($this->once())
                  ->method('execute');

        $this->worker->processQueue('foo');
    }

    public function testWorkerCountsRuns()
    {
        $this->markTestSkipped('This test has been broken.');

        $this->options->setMaxRuns(2);

        $this->queue->expects($this->exactly(2))
                    ->method('pop')
                    ->will($this->returnValue($this->job));

        $this->worker->processQueue('foo');
    }

    public function testWorkerMaxMemory()
    {
        $this->markTestSkipped('This test has been broken.');

        $this->options->setMaxMemory(1);

        $this->queue->expects($this->exactly(1))
            ->method('pop');

        $this->assertTrue($this->worker->processQueue('foo') === 0);
    }

    public function testCorrectIdentifiersAreSetToEventManager()
    {
        $eventManager = $this->worker->getEventManager();

        $this->assertContains('SlmQueue\Worker\WorkerInterface', $eventManager->getIdentifiers());
        $this->assertContains('SlmQueueTest\Asset\SimpleWorker', $eventManager->getIdentifiers());
    }

    public function testEventManagerTriggersEvents()
    {
        $this->markTestSkipped('This test has been broken.');

        $eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');
        $this->worker->setEventManager($eventManager);

        $this->queue->expects($this->once())
                    ->method('pop')
                    ->will($this->returnValue($this->job));

        // Trigger will be called 4: one for process queue pre, post, and process job pre, post

        $eventManager->expects($this->exactly(4))
                     ->method('trigger');

        $eventManager->expects($this->at(0))
                     ->method('trigger')
                     ->with($this->equalTo(WorkerEvent::EVENT_PROCESS_QUEUE_PRE));

        $eventManager->expects($this->at(1))
                     ->method('trigger')
                     ->with($this->equalTo(WorkerEvent::EVENT_PROCESS_JOB_PRE));

        $eventManager->expects($this->at(2))
                     ->method('trigger')
                     ->with($this->equalTo(WorkerEvent::EVENT_PROCESS_JOB_POST));

        $eventManager->expects($this->at(3))
                     ->method('trigger')
                     ->with($this->equalTo(WorkerEvent::EVENT_PROCESS_QUEUE_POST));

        $this->worker->processQueue('foo');
    }

    public function testMethod_hasMemoryExceeded()
    {
        $this->markTestSkipped('This test has been broken.');

        $this->options->setMaxMemory(10000000000);
        $this->assertFalse($this->worker->isMaxMemoryExceeded());

        $this->options->setMaxMemory(1);
        $this->assertTrue($this->worker->isMaxMemoryExceeded());
    }

    public function testMethod_willExceedMaxRuns()
    {
        $this->markTestSkipped('This test has been broken.');

        $this->options->setMaxRuns(10);
        $this->assertFalse($this->worker->isMaxRunsReached(9));
        $this->assertTrue($this->worker->isMaxRunsReached(10));
        $this->assertTrue($this->worker->isMaxRunsReached(11));
    }
}
