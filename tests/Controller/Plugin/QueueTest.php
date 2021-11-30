<?php

namespace SlmQueueTest\Controller\Plugin;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use SlmQueue\Controller\Exception\QueueNotFoundException;
use SlmQueue\Controller\Plugin\QueuePlugin;
use SlmQueue\Job\JobPluginManager;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueue\Worker\WorkerPluginManager;
use SlmQueueTest\Asset\SimpleJob;
use SlmQueueTest\Asset\SimpleQueue;

class QueueTest extends TestCase
{
    public function testPluginCreatesQueueFromPluginManager(): void
    {
        $serviceManager = new ServiceManager();
        $queuePluginManager = $this->createMock(QueuePluginManager::class, [], [$serviceManager]);
        $jobPluginManager = $this->createMock(JobPluginManager::class, [], [$serviceManager]);
        $workerPluginManager = $this->createMock(WorkerPluginManager::class);

        $queue = new SimpleQueue('DefaultQueue', $jobPluginManager, $workerPluginManager);

        $queuePluginManager->expects($this->once())
            ->method('has')
            ->with('DefaultQueue')
            ->will($this->returnValue(true));

        $queuePluginManager->expects($this->once())
            ->method('get')
            ->with('DefaultQueue')
            ->will($this->returnValue($queue));

        $plugin = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke('DefaultQueue');
    }

    public function testPluginThrowsExceptionWhenQueueDoesNotExists(): void
    {
        $serviceManager = new ServiceManager();
        $queuePluginManager = $this->createMock(QueuePluginManager::class, [], [$serviceManager]);
        $jobPluginManager = $this->createMock(JobPluginManager::class, [], [$serviceManager]);

        $queuePluginManager->expects($this->once())
            ->method('has')
            ->with('DefaultQueue')
            ->will($this->returnValue(false));

        $this->expectException(QueueNotFoundException::class);

        $plugin = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke('DefaultQueue');
    }

    public function testPluginThrowsExceptionWhenNoQueueIsSet(): void
    {
        $serviceManager = new ServiceManager();
        $queuePluginManager = $this->createMock(QueuePluginManager::class, [], [$serviceManager]);
        $jobPluginManager = $this->createMock(JobPluginManager::class, [], [$serviceManager]);
        $plugin = new QueuePlugin($queuePluginManager, $jobPluginManager);

        $this->expectException(QueueNotFoundException::class);
        $plugin->push('TestJob');
    }

    public function testPluginPushesJobIntoQueue(): void
    {
        $serviceManager = new ServiceManager();
        $queuePluginManager = new QueuePluginManager($serviceManager);
        $jobPluginManager = new JobPluginManager($serviceManager);

        $name = 'DefaultQueue';
        $queue = $this->createMock(SimpleQueue::class, ['push'], [$name, $jobPluginManager]);
        $job = new SimpleJob();

        $queue->expects($this->once())
            ->method('push')
            ->with($job)
            ->will($this->returnValue(null));
        $queuePluginManager->setService($name, $queue);
        $jobPluginManager->setService('SimpleJob', $job);

        $plugin = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke($name);

        $result = $plugin->push('SimpleJob');
        static::assertSame($job, $result);
    }

    public function testPayloadCanBeInjectedViaPlugin(): void
    {
        $serviceManager = new ServiceManager();
        $queuePluginManager = new QueuePluginManager($serviceManager);
        $jobPluginManager = new JobPluginManager($serviceManager);

        $name = 'DefaultQueue';
        $queue = $this->createMock(SimpleQueue::class, ['push'], [$name, $jobPluginManager]);
        $job = new SimpleJob();

        $queue->expects($this->once())
            ->method('push')
            ->with($job)
            ->will($this->returnValue(null));
        $queuePluginManager->setService($name, $queue);
        $jobPluginManager->setService('SimpleJob', $job);

        $plugin = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke($name);

        $payload = ['foo' => 'bar'];
        $result = $plugin->push('SimpleJob', $payload);

        static::assertSame($payload, $result->getContent());
    }

    public function testPluginPushesJobIntoQueueWithPushOptions(): void
    {
        $serviceManager = new ServiceManager();
        $queuePluginManager = new QueuePluginManager($serviceManager);
        $jobPluginManager = new JobPluginManager($serviceManager);
        $workerPluginManager = $this->createMock(WorkerPluginManager::class);

        $name = 'DefaultQueue';
        $queue = new SimpleQueue('queue', $jobPluginManager, $workerPluginManager);
        $job = new SimpleJob();

        $queuePluginManager->setService($name, $queue);
        $jobPluginManager->setService('SimpleJob', $job);

        $plugin = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke($name);

        $options = ['foo' => 'bar'];
        $result = $plugin->push('SimpleJob', null, $options);

        static::assertSame($queue->getUsedOptions(), $options);
    }

    public function testPluginPushesJobIntoQueueWithoutPushOptions(): void
    {
        $serviceManager = new ServiceManager();
        $queuePluginManager = new QueuePluginManager($serviceManager);
        $jobPluginManager = new JobPluginManager($serviceManager);
        $workerPluginManager = $this->createMock(WorkerPluginManager::class);

        $name = 'DefaultQueue';
        $queue = new SimpleQueue('queue', $jobPluginManager, $workerPluginManager);
        $job = new SimpleJob();

        $queuePluginManager->setService($name, $queue);
        $jobPluginManager->setService('SimpleJob', $job);

        $plugin = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke($name);

        $result = $plugin->push('SimpleJob');

        static::assertSame($queue->getUsedOptions(), []);
    }

    public function testPluginPushesJobInstance(): void
    {
        $serviceManager = new ServiceManager();
        $queuePluginManager = new QueuePluginManager($serviceManager);
        $jobPluginManager = new JobPluginManager($serviceManager);
        $workerPluginManager = $this->createMock(WorkerPluginManager::class);

        $queue = new SimpleQueue('default', $jobPluginManager, $workerPluginManager);

        $queuePluginManager->setService('default', $queue);

        $job = new SimpleJob();
        $job->setMetadata(['a' => 'b']);
        $job->setContent(123);

        $plugin = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke('default')->pushJob($job, ['delay' => 3]);

        static::assertSame(['delay' => 3], $queue->getUsedOptions());

        $poppedJob = $queue->pop();
        static::assertInstanceOf(SimpleJob::class, $poppedJob);
        static::assertEquals('b', $poppedJob->getMetadata('a'));
        static::assertEquals(123, $poppedJob->getContent());
    }

    public function testPluginThrowsExceptionWhenQueueNotSet(): void
    {
        $serviceManager = new ServiceManager();
        $queuePluginManager = new QueuePluginManager($serviceManager);
        $jobPluginManager = new JobPluginManager($serviceManager);

        $plugin = new QueuePlugin($queuePluginManager, $jobPluginManager);

        $this->expectException(QueueNotFoundException::class);

        $plugin->__invoke('default')->pushJob(new SimpleJob());
    }
}
