<?php

namespace SlmQueueTest\Strategy;

use PHPUnit_Framework_TestCase;
use SlmQueue\Strategy\AttachQueueListenersStrategy;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Event\BootstrapEvent;
use SlmQueueTest\Asset\SimpleWorker;
use Zend\EventManager\ListenerAggregateInterface;

class AttachQueueListenersStrategyTest extends PHPUnit_Framework_TestCase
{
    protected $queue;
    protected $worker;
    /** @var AttachQueueListenersStrategy */
    protected $listener;
    protected $strategyManager;

    public function setUp()
    {
        $this->queue           = $this->getMock(\SlmQueue\Queue\QueueInterface::class);
        $this->worker          = new SimpleWorker();
        $this->strategyManager = $this
            ->getMockBuilder('SlmQueue\Strategy\StrategyPluginManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->listener        = new AttachQueueListenersStrategy($this->strategyManager, [
            'queueName' => [
                'SlmQueue\Strategy\SomeStrategy',
            ]
        ]);
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        static::assertInstanceOf(\SlmQueue\Strategy\AbstractStrategy::class, $this->listener);
    }

    public function testListensToCorrectEventAtCorrectPriority()
    {
        $evm      = $this->getMock(\Zend\EventManager\EventManagerInterface::class);
        $priority = 1;

        $evm->expects($this->at(0))->method('attach')
            ->with(WorkerEventInterface::EVENT_BOOTSTRAP, [$this->listener, 'attachQueueListeners'], PHP_INT_MAX);

        $this->listener->attach($evm, $priority);
    }

    public function testAttachQueueListenersDetachedSelfFromEventManager()
    {
        $eventManager = $this->getMock('Zend\EventManager\EventManager');
        $this->worker = new SimpleWorker($eventManager);

        $eventManager->expects($this->at(0))->method('attach')
            ->with(WorkerEventInterface::EVENT_BOOTSTRAP, [$this->listener, 'attachQueueListeners'])
            ->willReturn([$this->listener, 'attachQueueListeners']);

        $eventManager->expects($this->once())->method('detach')
            ->with([$this->listener, 'attachQueueListeners'])
            ->willReturn(true);

        $this->queue->expects($this->once())->method('getName')->will($this->returnValue('queueName'));
        $strategyMock = $this->getMock('SlmQueueTest\Asset\SimpleStrategy');
        $this->strategyManager->expects($this->once())->method('get')->willReturn($strategyMock);

        // will attach WorkerEventInterface::EVENT_BOOTSTRAP callback to listener
        $this->listener->attach($eventManager);

        // should detach WorkerEventInterface::EVENT_BOOTSTRAP
        $this->listener->attachQueueListeners(new BootstrapEvent($this->worker, $this->queue));
    }

    public function testAttachQueueListenerFallbackToDefaultIfQueueNameIsNotMatched()
    {
        // let's change the config setup
        $class          = new \ReflectionClass(\SlmQueue\Strategy\AttachQueueListenersStrategy::class);
        $strategyConfig = $class->getProperty('strategyConfig');
        $strategyConfig->setAccessible(true);
        $strategyConfig->setValue($this->listener, [
            'default' => [
                \SlmQueueTest\Asset\SimpleStrategy::class,
            ]
        ]);

        $this->queue->expects($this->once())->method('getName')->willreturn('nonConfiguredQueueName');
        $strategyMock = $this->getMock(\SlmQueueTest\Asset\SimpleStrategy::class);
        $strategyMock->expects($this->exactly(1))->method('attach');
        $this->strategyManager->expects($this->at(0))->method('get')->with(\SlmQueueTest\Asset\SimpleStrategy::class,
            [])->willReturn($strategyMock);

        $this->listener->attachQueueListeners(new BootstrapEvent($this->worker, $this->queue));
    }

    public function testAttachQueueListenersStrategyConfig()
    {
        // let's change the config setup
        $class          = new \ReflectionClass(\SlmQueue\Strategy\AttachQueueListenersStrategy::class);
        $strategyConfig = $class->getProperty('strategyConfig');
        $strategyConfig->setAccessible(true);
        $strategyConfig->setValue($this->listener, [
            'queueName' => [
                'SlmQueue\Strategy\SomeStrategy',
                'SlmQueue\Strategy\OtherStrategy' => ['priority' => 3],
                'SlmQueue\Strategy\FinalStrategy' => ['foo' => 'bar'],
                'SlmQueue\Strategy\SomeStrategy'  => 'not_an_array',
            ]
        ]);

        $eventManager = $this->worker->getEventManager();
        $this->queue->expects($this->once())->method('getName')->willreturn('queueName');

        $strategyMock = $this->getMock(ListenerAggregateInterface::class);
        $strategyMock->expects($this->exactly(1))->method('attach')->with($eventManager);
        $this->strategyManager->expects($this->at(0))->method('get')->with('SlmQueue\Strategy\SomeStrategy',
            [])->willReturn($strategyMock);

        $strategyMock = $this->getMock(ListenerAggregateInterface::class);
        $strategyMock->expects($this->exactly(1))->method('attach')->with($eventManager, 3);
        $this->strategyManager->expects($this->at(1))->method('get')->with('SlmQueue\Strategy\OtherStrategy',
            [])->willReturn($strategyMock);

        $strategyMock = $this->getMock(ListenerAggregateInterface::class);
        $strategyMock->expects($this->exactly(1))->method('attach')->with($eventManager);
        $this->strategyManager->expects($this->at(2))->method('get')->with('SlmQueue\Strategy\FinalStrategy',
            ['foo' => 'bar'])->willReturn($strategyMock);

        $this->listener->attachQueueListeners(new BootstrapEvent($this->worker, $this->queue));
    }

    public function testAttachQueueListenersBootstrapEventIsTriggeredOnlyOnce()
    {
        $eventManager = $this->getMock(\Zend\EventManager\EventManager::class);
        $this->worker = new SimpleWorker($eventManager);
        $this->queue->expects($this->once())->method('getName')->willreturn('queueName');

        $strategyMock = $this->getMock(ListenerAggregateInterface::class);
        $strategyMock->expects($this->exactly(1))->method('attach')->with($eventManager);
        $this->strategyManager->expects($this->at(0))->method('get')->with('SlmQueue\Strategy\SomeStrategy',
            [])->willReturn($strategyMock);

        $eventManager->expects($this->once())->method('triggerEvent');
        $this->listener->attachQueueListeners(new BootstrapEvent($this->worker, $this->queue));
    }
}
