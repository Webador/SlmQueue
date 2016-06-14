<?php

namespace SlmQueueTest\Strategy;

use PHPUnit_Framework_TestCase;
use SlmQueue\Strategy\AttachQueueListenersStrategy;
use SlmQueue\Worker\WorkerEvent;
use SlmQueueTest\Asset\SimpleJob;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\ServiceManager\ServiceManager;

class AttachQueueListenersStrategyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AttachQueueListenersStrategy
     */
    protected $listener;

    /**
     * @var WorkerEvent
     */
    protected $event;

    public function setUp()
    {
        $serviceManager = new ServiceManager();
        $jobPluginManager      = $this->getMock('SlmQueue\Job\JobPluginManager', [], [$serviceManager]);
        $queue                 = $this->getMock(
            'SlmQueue\Queue\AbstractQueue',
            [],
            ['queueName', $jobPluginManager]
        );
        $this->strategyPluginManager = $this->getMock('SlmQueue\Strategy\StrategyPluginManager', [], [$serviceManager]);
        $eventManager          = $this->getMock('Zend\EventManager\EventManager');
        $worker                = $this->getMock('SlmQueue\Worker\AbstractWorker', [], [$eventManager]);

        $queue->expects($this->any())->method('getName')->will($this->returnValue('queueName'));
        $worker->expects($this->any())->method('getEventManager')->will($this->returnValue($eventManager));

        $event = new WorkerEvent($worker, $queue);
        $job   = new SimpleJob();

        $event->setJob($job);

        $this->listener = new AttachQueueListenersStrategy($this->strategyPluginManager, ['queueName' => [
            'SlmQueue\Strategy\SomeStrategy',
        ]]);

        $reflectionClass = new \ReflectionClass(AttachQueueListenersStrategy::class);
        $this->listenerProperty= $reflectionClass->getProperty('listeners');
        $this->listenerProperty->setAccessible(true);

        $class = new \ReflectionClass(\SlmQueue\Strategy\AttachQueueListenersStrategy::class);
        $this->strategyConfig = $class->getProperty('strategyConfig');
        $this->strategyConfig->setAccessible(true);

        $this->event    = $event;
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        $this->assertInstanceOf('SlmQueue\Strategy\AbstractStrategy', $this->listener);
    }

    public function testListensToCorrectEvents()
    {
        $evm = $this->getMock('Zend\EventManager\EventManagerInterface');

        $evm->expects($this->at(0))->method('attach')
            ->with(WorkerEvent::EVENT_BOOTSTRAP, [$this->listener, 'attachQueueListeners']);

        $this->listener->attach($evm);
    }

    public function testAttachQueueListenersDetachedSelfFromEventManager()
    {
        $workerMock       = $this->event->getTarget();
        $eventManagerMock = $workerMock->getEventManager();
        $eventManagerMock ->expects($this->at(0))->method('attach')
            ->with(WorkerEvent::EVENT_BOOTSTRAP, [$this->listener, 'attachQueueListeners'])
            ->willReturn([$this->listener, 'attachQueueListeners']);

        $eventManagerMock ->expects($this->any())->method('detach')
            ->with([$this->listener, 'attachQueueListeners'])
            ->willReturn(true);

        $this->listener->attach($eventManagerMock);

        $strategyMock          = $this->getMock('SlmQueueTest\Asset\SimpleStrategy');
        $strategyMock->expects($this->exactly(1))->method('attach');
        $this->strategyPluginManager->expects($this->at(0))->method('get')->willReturn($strategyMock);

        static::assertCount(1, $this->listenerProperty->getValue($this->listener));
        $this->listener->attachQueueListeners($this->event);
        static::assertCount(0, $this->listenerProperty->getValue($this->listener));
    }

    public function testAttachQueueListenerFallbackToDefaultIfQueueNameIsNotMatched()
    {
        $this->strategyConfig->setValue($this->listener, [
            'default' => [
                'SlmQueue\Strategy\SomeStrategy',
            ]
        ]);

        $strategyMock          = $this->getMock('SlmQueueTest\Asset\SimpleStrategy');
        $strategyMock->expects($this->exactly(1))->method('attach');
        $this->strategyPluginManager->expects($this->at(0))->method('get')->willReturn($strategyMock);

        $this->listener->attachQueueListeners($this->event);
    }

    public function testAttachQueueListenersStrategyConfig()
    {
        $workerMock       = $this->event->getTarget();
        $eventManagerMock = $workerMock->getEventManager();

        $class = new \ReflectionClass('SlmQueue\Strategy\AttachQueueListenersStrategy');
        $property = $class->getProperty('strategyConfig');
        $property->setAccessible(true);

        $property->setValue($this->listener, ['queueName' => [
                'SlmQueue\Strategy\SomeStrategy',
                'SlmQueue\Strategy\OtherStrategy' => ['priority' => 3],
                'SlmQueue\Strategy\FinalStrategy' => ['foo' => 'bar'],
                'SlmQueue\Strategy\SomeStrategy' => 'not_an_array',
            ]]);

        $strategyMock          = $this->getMock('SlmQueueTest\Asset\SimpleStrategy');
        $strategyMock->expects($this->exactly(3))->method('attach');
        $this->strategyPluginManager->expects($this->at(0))->method('get')->with('SlmQueue\Strategy\SomeStrategy', [])->willReturn($strategyMock);
        $this->strategyPluginManager->expects($this->at(1))->method('get')->with('SlmQueue\Strategy\OtherStrategy', [])->willReturn($strategyMock);
        $this->strategyPluginManager->expects($this->at(2))->method('get')->with('SlmQueue\Strategy\FinalStrategy', ['foo' => 'bar'])->willReturn($strategyMock);

        $this->listener->attachQueueListeners($this->event);
    }

    public function testAttachQueueListenersBootstrapEventIsTriggeredOnlyOnce()
    {
        $workerMock       = $this->event->getTarget();
        $eventManagerMock = $workerMock->getEventManager();

        $strategyMock          = $this->getMock('SlmQueueTest\Asset\SimpleStrategy');
        $strategyMock->expects($this->exactly(1))->method('attach');
        $this->strategyPluginManager->expects($this->at(0))->method('get')->willReturn($strategyMock);

        $eventManagerMock->expects($this->once())->method('triggerEvent')->with($this->event);
        $this->listener->attachQueueListeners($this->event);
    }
}
