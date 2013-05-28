<?php

namespace SlmQueueTest\Worker;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Options\WorkerOptions;
use SlmQueueTest\Asset\SimpleWorker;

class AbstractWorkerTest extends TestCase
{
    protected $worker, $plugins, $options, $queue, $job;

    public function setUp()
    {
        $queueName = 'foo';
        $options   = new WorkerOptions;
        $options->setMaxRuns(1);
        $options->setMaxMemory(1024*1024*1024);

        $plugins = $this->getMock('SlmQueue\Queue\QueuePluginManager', array('get'));
        $worker  = new SimpleWorker($plugins, $options);

        $queue   = $this->getMock('SlmQueue\Queue\QueueInterface');
        $plugins->expects($this->once())
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
        $this->queue->expects($this->once())
                    ->method('pop')
                    ->will($this->returnValue($this->job));

        $this->worker->processQueue('foo');
    }

    public function testWorkerExecutesJob()
    {
        $this->queue->expects($this->once())
                    ->method('pop')
                    ->will($this->returnValue($this->job));

        $this->job->expects($this->once())
                  ->method('execute');

        $this->worker->processQueue('foo');
    }

    public function testWorkerCountsRuns()
    {
        $this->options->setMaxRuns(2);

        $this->queue->expects($this->exactly(2))
                    ->method('pop')
                    ->will($this->returnValue($this->job));

        $this->worker->processQueue('foo');
    }

    public function testWorkerInjectsQueueForAwareInterface()
    {
        $job = $this->getMock('SlmQueueTest\Asset\QueueAwareJob', array('setQueue'));
        $job->expects($this->once())
            ->method('setQueue')
            ->with($this->queue);

        $this->queue->expects($this->once())
                    ->method('pop')
                    ->will($this->returnValue($job));

        $this->worker->processQueue('foo');
    }
}
