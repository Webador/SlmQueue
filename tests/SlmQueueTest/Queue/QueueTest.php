<?php

namespace SlmQueueTest\Job;

use DateTime;
use PHPUnit_Framework_TestCase as TestCase;
use SlmQueueTest\Asset\QueueAwareJob;
use SlmQueueTest\Asset\SimpleQueue;
use SlmQueueTest\Asset\SimpleJob;

class QueueTest extends TestCase
{
    protected $job;
    protected $jobName;
    protected $jobPluginManager;
    protected $queue;

    public function setUp()
    {
        $this->job     = new SimpleJob;
        $this->jobName = 'SlmQueueTest\Asset\SimpleJob';

        $this->jobPluginManager = $this->getMock('SlmQueue\Job\JobPluginManager');
        $this->queue = new SimpleQueue('queue', $this->jobPluginManager);
    }

    public function testCanPushThenPopJob()
    {
        $this->jobPluginManager->expects($this->once())
                               ->method('get')
                               ->with($this->jobName)
                               ->will($this->returnValue($this->job));

        $this->queue->push($this->job);
        $job = $this->queue->pop();

        $this->assertInstanceOf($this->jobName, $job);

        $expected = spl_object_hash($this->job);
        $actual   = spl_object_hash($job);
        $this->assertEquals($expected, $actual);
    }

    public function testCanPushThenPopWithJobContent()
    {
        $this->jobPluginManager->expects($this->once())
                               ->method('get')
                               ->with($this->jobName)
                               ->will($this->returnValue($this->job));

        $this->job->setContent('Foo');

        $this->queue->push($this->job);
        $job = $this->queue->pop();

        $this->assertEquals('Foo', $job->getContent());
    }

    public function testCanPushThenPopWithJobMetadata()
    {
        $this->jobPluginManager->expects($this->once())
                               ->method('get')
                               ->with($this->jobName)
                               ->will($this->returnValue($this->job));

        $this->job->setMetadata('Foo', 'Bar');

        $this->queue->push($this->job);
        $job = $this->queue->pop();

        $this->assertEquals(array('Foo' => 'Bar'), $job->getMetadata());
        $this->assertEquals('Bar', $job->getMetadata('Foo'));
    }

    public function testCorrectlySerializeJobContent()
    {
        $job = new SimpleJob();
        $job->setContent('Foo');

        $expected = '{"name":"SlmQueueTest\\\Asset\\\SimpleJob","content":"s:3:\"Foo\";","metadata":[]}';
        $actual   = $this->queue->serializeJob($job);

        $this->assertEquals($expected, $actual);
    }

    public function testCorrectlySerializeJobMetadata()
    {
        $job = new SimpleJob();
        $job->setMetadata('Foo', 'Bar');

        $expected = '{"name":"SlmQueueTest\\\Asset\\\SimpleJob","content":"N;","metadata":{"Foo":"Bar"}}';
        $actual   = $this->queue->serializeJob($job);

        $this->assertEquals($expected, $actual);
    }

    public function testCorrectlySerializeJobContentAndMetadata()
    {
        $job = new SimpleJob();
        $job->setContent('Foo');
        $job->setMetadata('Foo', 'Bar');

        $expected = '{"name":"SlmQueueTest\\\Asset\\\SimpleJob","content":"s:3:\"Foo\";","metadata":{"Foo":"Bar"}}';
        $actual   = $this->queue->serializeJob($job);

        $this->assertEquals($expected, $actual);
    }

    public function testCorrectlySerializeJobServiceName()
    {
        $job = new SimpleJob();
        $job->setMetadata('name', 'SimpleJob');

        $expected = '{"name":"SimpleJob","content":"N;","metadata":{"name":"SimpleJob"}}';
        $actual   = $this->queue->serializeJob($job);

        $this->assertEquals($expected, $actual);
    }

    public function testCanCreateJobWithFQCN()
    {
        $this->jobPluginManager->expects($this->once())
                               ->method('get')
                               ->with($this->jobName)
                               ->will($this->returnValue($this->job));

        $payload = '{"name":"SlmQueueTest\\\Asset\\\SimpleJob","content":"N;","metadata":[]}';
        $job     = $this->queue->unserializeJob($payload);

        $expected = spl_object_hash($this->job);
        $actual   = spl_object_hash($job);
        $this->assertEquals($expected, $actual);
    }

    public function testCanCreateJobWithStringName()
    {
        $this->jobPluginManager->expects($this->once())
                               ->method('get')
                               ->with('SimpleJob')
                               ->will($this->returnValue($this->job));

        $payload = '{"name":"SimpleJob","content":"N;","metadata":[]}';
        $job     = $this->queue->unserializeJob($payload);

        $expected = spl_object_hash($this->job);
        $actual   = spl_object_hash($job);
        $this->assertEquals($expected, $actual);
    }

    public function testCanCreateJobWithContent()
    {
        $job = $this->getMock('SlmQueue\Job\JobInterface');
        $job->expects($this->once())
            ->method('setContent')
            ->with('Foo')
            ->will($this->returnValue($job));

        $jobPluginManager = $this->getMock('SlmQueue\Job\JobPluginManager');
        $jobPluginManager->expects($this->once())
                         ->method('get')
                         ->with('SimpleJob')
                         ->will($this->returnValue($job));

        $queue  = new SimpleQueue('queue', $jobPluginManager);
        $result = $queue->createJob('SimpleJob', serialize('Foo'));
    }

    public function testCanCreateJobWithMetadata()
    {
        $job = $this->getMock('SlmQueue\Job\JobInterface');
        $job->expects($this->once())
            ->method('setContent')
            ->will($this->returnValue($job));

        $job->expects($this->once())
            ->method('setMetadata')
            ->with(array('foo' => 'Bar'))
            ->will($this->returnValue($job));

        $jobPluginManager = $this->getMock('SlmQueue\Job\JobPluginManager');
        $jobPluginManager->expects($this->once())
                         ->method('get')
                         ->with('SimpleJob')
                         ->will($this->returnValue($job));

        $queue  = new SimpleQueue('queue', $jobPluginManager);
        $result = $queue->createJob('SimpleJob', null, array('foo' => 'Bar'));
    }

    public function testCreateQueueAwareJob()
    {
        $job = new QueueAwareJob();

        $jobPluginManager = $this->getMock('SlmQueue\Job\JobPluginManager');
        $jobPluginManager->expects($this->once())
                         ->method('get')
                         ->with('SlmQueueTest\Asset\QueueAwareJob')
                         ->will($this->returnValue($job));

        $queue = new SimpleQueue('queue', $jobPluginManager);

        $result = $queue->createJob('SlmQueueTest\Asset\QueueAwareJob');

        $this->assertSame($queue, $result->getQueue());
    }
}
