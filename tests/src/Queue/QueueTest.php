<?php

namespace SlmQueueTest\Queue;

use JsonException;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SlmQueue\Job\JobInterface;
use SlmQueue\Job\JobPluginManager;
use SlmQueue\Queue\QueueInterface;
use SlmQueueTest\Asset\BinaryJob;
use SlmQueueTest\Asset\QueueAwareJob;
use SlmQueueTest\Asset\SimpleJob;
use SlmQueueTest\Asset\SimpleQueue;

class QueueTest extends TestCase
{
    /**
     * @var JobPluginManager&MockObject
     */
    private JobPluginManager $jobPluginManager;

    private JobInterface $job;
    private string $jobName;
    private QueueInterface $queue;

    public function setUp(): void
    {
        $this->job = new SimpleJob();
        $this->jobName = SimpleJob::class;
        $serviceManager = new ServiceManager();

        $this->binaryJob = new BinaryJob();
        $this->binaryJobName = BinaryJob::class;

        $this->jobPluginManager = $this->createMock(JobPluginManager::class, [], [$serviceManager]);
        $this->queue = new SimpleQueue('queue', $this->jobPluginManager);
    }

    public function testCanPushThenPopJob(): void
    {
        $this->jobPluginManager->expects($this->once())
            ->method('get')
            ->with($this->jobName)
            ->will($this->returnValue($this->job));

        $this->queue->push($this->job);
        $job = $this->queue->pop();

        static::assertInstanceOf($this->jobName, $job);

        $expected = spl_object_hash($this->job);
        $actual = spl_object_hash($job);
        static::assertEquals($expected, $actual);
    }

    public function testCanPushThenPopWithJobContent(): void
    {
        $this->jobPluginManager->expects($this->once())
            ->method('get')
            ->with($this->jobName)
            ->will($this->returnValue($this->job));

        $this->job->setContent('Foo');

        $this->queue->push($this->job);
        $job = $this->queue->pop();

        static::assertEquals('Foo', $job->getContent());
    }

    public function testCanPushThenPopWithJobMetadata(): void
    {
        $this->jobPluginManager->expects($this->once())
            ->method('get')
            ->with($this->jobName)
            ->will($this->returnValue($this->job));

        $this->job->setMetadata('Foo', 'Bar');

        $this->queue->push($this->job);
        $job = $this->queue->pop();

        // metadata will have reserved __name__ key with FQCN
        $expected = ['Foo' => 'Bar'] + ['__name__' => 'SlmQueueTest\Asset\SimpleJob'];

        static::assertEquals($expected, $job->getMetadata());
        static::assertEquals('Bar', $job->getMetadata('Foo'));
    }

    public function testCorrectlySerializeJobContent(): void
    {
        $job = new SimpleJob();
        $job->setContent('Foo');

        $expected = '{"content":"s:3:\"Foo\";","metadata":{"__name__":"SlmQueueTest\\\Asset\\\SimpleJob"}}';
        $actual = $this->queue->serializeJob($job);

        static::assertEquals($expected, $actual);
    }

    public function testCorrectlySerializeJobMetadata(): void
    {
        $job = new SimpleJob();
        $job->setMetadata('Foo', 'Bar');

        $expected = '{"content":"N;","metadata":{"Foo":"Bar","__name__":"SlmQueueTest\\\Asset\\\SimpleJob"}}';
        $actual = $this->queue->serializeJob($job);

        static::assertEquals($expected, $actual);
    }

    public function testCorrectlySerializeJobContentAndMetadata(): void
    {
        $job = new SimpleJob();
        $job->setContent('Foo');
        $job->setMetadata('Foo', 'Bar');

        $expected = '{"content":"s:3:\"Foo\";","metadata":{"Foo":"Bar","__name__":"SlmQueueTest\\\Asset\\\SimpleJob"}}';
        $actual = $this->queue->serializeJob($job);

        static::assertEquals($expected, $actual);
    }

    public function testCorrectlySerializeJobServiceName(): void
    {
        $job = new SimpleJob();
        $job->setMetadata('__name__', 'SimpleJob');

        $expected = '{"content":"N;","metadata":{"__name__":"SimpleJob"}}';
        $actual = $this->queue->serializeJob($job);

        static::assertEquals($expected, $actual);
    }

    public function testInvalidSerializeJobContent(): void
    {
        $job = new SimpleJob();
        $job->setMetadata('__name__', 'SimpleJob');
        $job->setContent(chr(128));

        $this->expectException(JsonException::class);

        $this->queue->serializeJob($job);
    }

    public function testCanCreateJobWithFQCN(): void
    {
        $this->jobPluginManager->expects($this->once())
            ->method('get')
            ->with($this->jobName)
            ->will($this->returnValue($this->job));

        $payload = '{"content":"N;","metadata":{"__name__":"SlmQueueTest\\\Asset\\\SimpleJob"}}';
        $job = $this->queue->unserializeJob($payload);

        $expected = spl_object_hash($this->job);
        $actual = spl_object_hash($job);
        static::assertEquals($expected, $actual);
    }

    public function testCanCreateJobWithStringName(): void
    {
        $this->jobPluginManager->expects($this->once())
            ->method('get')
            ->with('SimpleJob')
            ->will($this->returnValue($this->job));

        $payload = '{"content":"N;","metadata":{"__name__":"SimpleJob"}}';
        $job = $this->queue->unserializeJob($payload);

        $expected = spl_object_hash($this->job);
        $actual = spl_object_hash($job);
        static::assertEquals($expected, $actual);
    }

    public function testCanCreateJobWithContent(): void
    {
        $this->jobPluginManager->expects($this->once())
            ->method('get')
            ->with($this->jobName)
            ->will($this->returnValue($this->job));

        $payload = '{"content":"s:3:\"Foo\";","metadata":{"__name__":"SlmQueueTest\\\Asset\\\SimpleJob"}}';
        $job = $this->queue->unserializeJob($payload);

        static::assertEquals('Foo', $job->getContent());
    }

    public function testCanCreateJobWithBinaryContent(): void
    {
        $this->jobPluginManager->expects($this->once())
            ->method('get')
            ->with($this->binaryJobName)
            ->will($this->returnValue($this->binaryJob));

        // 1x1px image
        $image = file_get_contents(dirname(__DIR__) . '/Asset/1x1px.png');

        $payload = '{"content":"' . base64_encode(serialize($image)) . '",' .
            '"metadata":{"__name__":"SlmQueueTest\\\Asset\\\BinaryJob"}}';
        $job = $this->queue->unserializeJob($payload);

        static::assertEquals($image, $job->getContent());
    }

    public function testCanCreateJobWithMetadata(): void
    {
        $this->jobPluginManager->expects($this->once())
            ->method('get')
            ->with($this->jobName)
            ->will($this->returnValue($this->job));

        $payload = '{"content":"N;","metadata":{"Foo":"Bar","__name__":"SlmQueueTest\\\Asset\\\SimpleJob"}}';
        $job = $this->queue->unserializeJob($payload);

        static::assertEquals('Bar', $job->getMetadata('Foo'));
    }

    public function testCreateQueueAwareJob(): void
    {
        $job = new QueueAwareJob();
        $this->jobPluginManager->expects($this->once())
            ->method('get')
            ->with('QueueAwareJob')
            ->will($this->returnValue($job));

        $payload = '{"__name__":"QueueAwareJob","content":"N;","metadata":{"__name__":"QueueAwareJob"}}';
        $this->queue->unserializeJob($payload);

        static::assertSame($this->queue, $job->getQueue());
    }
}
