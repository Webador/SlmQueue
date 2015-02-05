<?php

namespace SlmQueueTest\Queue;

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

        // metadata will have reserved __name__ key with FQCN
        $expected = array('Foo' => 'Bar') + array('__name__' => 'SlmQueueTest\Asset\SimpleJob');

        $this->assertEquals($expected, $job->getMetadata());
        $this->assertEquals('Bar', $job->getMetadata('Foo'));
    }

    public function testCorrectlySerializeJobContent()
    {
        $job = new SimpleJob();
        $job->setContent('Foo');

        $expected = '{"content":"s:3:\"Foo\";","metadata":{"__name__":"SlmQueueTest\\\Asset\\\SimpleJob"}}';
        $actual   = $this->queue->serializeJob($job);

        $this->assertEquals($expected, $actual);
    }

    public function testCorrectlySerializeJobMetadata()
    {
        $job = new SimpleJob();
        $job->setMetadata('Foo', 'Bar');

        $expected = '{"content":"N;","metadata":{"Foo":"Bar","__name__":"SlmQueueTest\\\Asset\\\SimpleJob"}}';
        $actual   = $this->queue->serializeJob($job);

        $this->assertEquals($expected, $actual);
    }

    public function testCorrectlySerializeJobContentAndMetadata()
    {
        $job = new SimpleJob();
        $job->setContent('Foo');
        $job->setMetadata('Foo', 'Bar');

        $expected = '{"content":"s:3:\"Foo\";","metadata":{"Foo":"Bar","__name__":"SlmQueueTest\\\Asset\\\SimpleJob"}}';
        $actual   = $this->queue->serializeJob($job);

        $this->assertEquals($expected, $actual);
    }

    public function testCorrectlySerializeJobServiceName()
    {
        $job = new SimpleJob();
        $job->setMetadata('__name__', 'SimpleJob');

        $expected = '{"content":"N;","metadata":{"__name__":"SimpleJob"}}';
        $actual   = $this->queue->serializeJob($job);

        $this->assertEquals($expected, $actual);
    }

    public function testCorrectlySerializeJobChain()
    {
        $job = new SimpleJob();
        $job->chainJob(new SimpleJob());

        $expected = '{"content":"N;","metadata":{"__jobchain__":["{\"content\":\"N;\",\"metadata\":{\"__name__\":\"SlmQueueTest\\\\\\\\Asset\\\\\\\\SimpleJob\"}}"],"__name__":"SlmQueueTest\\\\Asset\\\\SimpleJob"}}';
        $actual   = $this->queue->serializeJob($job);

        $this->assertEquals($expected, $actual);
    }

    public function testCanCreateJobWithFQCN()
    {
        $this->jobPluginManager->expects($this->once())
                               ->method('get')
                               ->with($this->jobName)
                               ->will($this->returnValue($this->job));

        $payload = '{"content":"N;","metadata":{"__name__":"SlmQueueTest\\\Asset\\\SimpleJob"}}';
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

        $payload = '{"content":"N;","metadata":{"__name__":"SimpleJob"}}';
        $job     = $this->queue->unserializeJob($payload);

        $expected = spl_object_hash($this->job);
        $actual   = spl_object_hash($job);
        $this->assertEquals($expected, $actual);
    }

    public function testCanCreateWithJobChain()
    {
        $chainedJob = new SimpleJob();
        $chainedJob->setContent('foo');
        $this->job->chainJob($chainedJob);

        $this->jobPluginManager->expects($this->at(0))
            ->method('get')
            ->with($this->jobName)
            ->will($this->returnValue($chainedJob));
        $this->jobPluginManager->expects($this->at(1))
            ->method('get')
            ->with($this->jobName)
            ->will($this->returnValue($this->job));

        $payload = '{"content":"N;","metadata":{"__jobchain__":["{\"content\":\"s:3:\\\\\\"foo\\\\\\";\",\"metadata\":{\"__name__\":\"SlmQueueTest\\\\\\\\Asset\\\\\\\\SimpleJob\"}}"],"__name__":"SlmQueueTest\\\\Asset\\\\SimpleJob"}}';
        $job     = $this->queue->unserializeJob($payload);
        $chain   = $job->getMetadata('__jobchain__');

        $this->assertCount(1, $chain);
        $this->assertCount(0, $chain[0]->getMetadata('__jobchain__', array()));

        $expected = spl_object_hash($chainedJob);
        $actual   = spl_object_hash($chain[0]);
        $this->assertEquals($expected, $actual);

        $expected = spl_object_hash($this->job);
        $actual   = spl_object_hash($job);
        $this->assertEquals($expected, $actual);
    }

    public function testCanCreateJobWithContent()
    {
        $this->jobPluginManager->expects($this->once())
                               ->method('get')
                               ->with($this->jobName)
                               ->will($this->returnValue($this->job));

        $payload = '{"content":"s:3:\"Foo\";","metadata":{"__name__":"SlmQueueTest\\\Asset\\\SimpleJob"}}';
        $job     = $this->queue->unserializeJob($payload);

        $this->assertEquals('Foo', $job->getContent());
    }

    public function testCanCreateJobWithMetadata()
    {
        $this->jobPluginManager->expects($this->once())
                               ->method('get')
                               ->with($this->jobName)
                               ->will($this->returnValue($this->job));

        $payload = '{"content":"N;","metadata":{"Foo":"Bar","__name__":"SlmQueueTest\\\Asset\\\SimpleJob"}}';
        $job     = $this->queue->unserializeJob($payload);

        $this->assertEquals('Bar', $job->getMetadata('Foo'));
    }

    public function testCreateQueueAwareJob()
    {
        $job = new QueueAwareJob();
        $this->jobPluginManager->expects($this->once())
                               ->method('get')
                               ->with('QueueAwareJob')
                               ->will($this->returnValue($job));

        $payload = '{"__name__":"QueueAwareJob","content":"N;","metadata":{"__name__":"QueueAwareJob"}}';
        $this->queue->unserializeJob($payload);

        $this->assertSame($this->queue, $job->getQueue());
    }
}
