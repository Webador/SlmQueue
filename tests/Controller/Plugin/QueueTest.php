<?php

namespace SlmQueueTest\Controller\Plugin;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueueTest\Asset\QueueAwareJob;
use SlmQueueTest\Asset\SimpleJob;
use SlmQueueTest\Asset\SimpleQueue;
use SlmQueue\Controller\Exception\QueueNotFoundException;
use SlmQueue\Controller\Plugin\QueuePlugin;
use SlmQueue\Job\JobPluginManager;
use SlmQueue\Queue\QueuePluginManager;
use Zend\ServiceManager\ServiceManager;

class QueueTest extends TestCase
{
    public function testPluginCreatesQueueFromPluginManager()
    {
        $serviceManager = new ServiceManager();
        $queuePluginManager = $this->getMock(QueuePluginManager::class, [], [$serviceManager]);
        $jobPluginManager   = $this->getMock(JobPluginManager::class, [], [$serviceManager]);

        $queue = new SimpleQueue('DefaultQueue', $jobPluginManager);

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

    public function testPluginThrowsExceptionWhenQueueDoesNotExists()
    {
        $serviceManager = new ServiceManager();
        $queuePluginManager = $this->getMock(QueuePluginManager::class, [], [$serviceManager]);
        $jobPluginManager   = $this->getMock(JobPluginManager::class, [], [$serviceManager]);

        $queuePluginManager->expects($this->once())
            ->method('has')
            ->with('DefaultQueue')
            ->will($this->returnValue(false));

        $this->setExpectedException(QueueNotFoundException::class);

        $plugin = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke('DefaultQueue');
    }

    public function testPluginThrowsExceptionWhenNoQueueIsSet()
    {
        $serviceManager = new ServiceManager();
        $queuePluginManager = $this->getMock(QueuePluginManager::class, [], [$serviceManager]);
        $jobPluginManager   = $this->getMock(JobPluginManager::class, [], [$serviceManager]);
        $plugin             = new QueuePlugin($queuePluginManager, $jobPluginManager);

        $this->setExpectedException(QueueNotFoundException::class);
        $plugin->push('TestJob');

    }

    public function testPluginPushesJobIntoQueue()
    {
        $serviceManager = new ServiceManager();
        $queuePluginManager = new QueuePluginManager($serviceManager);
        $jobPluginManager   = new JobPluginManager($serviceManager);

        $name  = 'DefaultQueue';
        $queue = $this->getMock(SimpleQueue::class, ['push'], [$name, $jobPluginManager]);
        $job   = new SimpleJob;

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

    public function testPayloadCanBeInjectedViaPlugin()
    {
        $serviceManager = new ServiceManager();
        $queuePluginManager = new QueuePluginManager($serviceManager);
        $jobPluginManager   = new JobPluginManager($serviceManager);

        $name  = 'DefaultQueue';
        $queue = $this->getMock(SimpleQueue::class, ['push'], [$name, $jobPluginManager]);
        $job   = new SimpleJob;

        $queue->expects($this->once())
              ->method('push')
              ->with($job)
              ->will($this->returnValue(null));
        $queuePluginManager->setService($name, $queue);
        $jobPluginManager->setService('SimpleJob', $job);

        $plugin  = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke($name);

        $payload = ['foo' => 'bar'];
        $result  = $plugin->push('SimpleJob', $payload);

        static::assertSame($payload, $result->getContent());
    }
    
    public function testPluginPushesJobIntoQueueWithPushOptions()
    {
        $serviceManager = new ServiceManager();
        $queuePluginManager = new QueuePluginManager($serviceManager);
        $jobPluginManager   = new JobPluginManager($serviceManager);

        $name  = 'DefaultQueue';
        $queue = new SimpleQueue('queue', $jobPluginManager);
        $job   = new SimpleJob;

        $queuePluginManager->setService($name, $queue);
        $jobPluginManager->setService('SimpleJob', $job);

        $plugin  = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke($name);
    
        $options = ['foo' => 'bar'];
        $result = $plugin->push('SimpleJob', null, $options);
        
        static::assertSame($queue->getUsedOptions(), $options);
    }
    
    public function testPluginPushesJobIntoQueueWithoutPushOptions()
    {
        $serviceManager = new ServiceManager();
        $queuePluginManager = new QueuePluginManager($serviceManager);
        $jobPluginManager   = new JobPluginManager($serviceManager);
    
        $name  = 'DefaultQueue';
        $queue = new SimpleQueue('queue', $jobPluginManager);
        $job   = new SimpleJob;
    
        $queuePluginManager->setService($name, $queue);
        $jobPluginManager->setService('SimpleJob', $job);
    
        $plugin  = new QueuePlugin($queuePluginManager, $jobPluginManager);
        $plugin->__invoke($name);
    
        $result = $plugin->push('SimpleJob');
    
        static::assertSame($queue->getUsedOptions(), []);
    }
}
