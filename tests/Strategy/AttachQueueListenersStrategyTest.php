<?php

namespace SlmQueueTest\Strategy;

use PHPUnit_Framework_TestCase;
use SlmQueue\Strategy\AttachQueueListenersStrategy;
use SlmQueue\Worker\WorkerEvent;
use SlmQueueTest\Asset\SimpleJob;
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
        $strategyPluginManager = $this->getMock('SlmQueue\Strategy\StrategyPluginManager', [], [$serviceManager]);
        $eventManager          = $this->getMock('Zend\EventManager\EventManager');
        $worker                = $this->getMock('SlmQueue\Worker\AbstractWorker', [], [$eventManager]);
        $strategyMock          = $this->getMock('SlmQueue\Strategy\AbstractStrategy');

        $queue->expects($this->any())->method('getName')->will($this->returnValue('queueName'));
        $worker->expects($this->any())->method('getEventManager')->will($this->returnValue($eventManager));
        $strategyPluginManager->expects($this->any())->method('get')->will($this->returnValue($strategyMock));

        $event = new WorkerEvent($worker, $queue);
        $job   = new SimpleJob();

        $event->setJob($job);

        $this->listener = new AttachQueueListenersStrategy($strategyPluginManager, ['queueName' => [
            'SlmQueue\Strategy\SomeStrategy',
        ]]);

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
        $eventManagerMock->expects($this->once())->method('detachAggregate')->with($this->listener);

        $eventManagerMock->expects($this->any())->method('getEvents')->will($this->returnValue([WorkerEvent::EVENT_PROCESS_QUEUE]));
        $eventManagerMock->expects($this->once())->method('trigger');

        $this->listener->attachQueueListeners($this->event);
    }

    public function testAttachQueueListenerFallbackToDefaultIfQueueNameIsNotMatched()
    {
        $workerMock       = $this->event->getTarget();
        $eventManagerMock = $workerMock->getEventManager();
        $eventManagerMock->expects($this->any())->method('getEvents')->will($this->returnValue([WorkerEvent::EVENT_PROCESS_QUEUE]));

        $class = new \ReflectionClass('SlmQueue\Strategy\AttachQueueListenersStrategy');
        $property = $class->getProperty('strategyConfig');
        $property->setAccessible(true);

        $property->setValue($this->listener, [
            'default' => [
                'SlmQueue\Strategy\SomeStrategy',
            ]
        ]);

        $this->listener->attachQueueListeners($this->event);
    }

    public function testAttachQueueListenersStrategyConfig()
    {
        $workerMock       = $this->event->getTarget();
        $eventManagerMock = $workerMock->getEventManager();
        $eventManagerMock->expects($this->any())->method('getEvents')->will($this->returnValue([WorkerEvent::EVENT_PROCESS_QUEUE]));

        $class = new \ReflectionClass('SlmQueue\Strategy\AttachQueueListenersStrategy');
        $property = $class->getProperty('strategyConfig');
        $property->setAccessible(true);

        $property->setValue($this->listener, ['queueName' => [
                'SlmQueue\Strategy\SomeStrategy',
                'SlmQueue\Strategy\OtherStrategy' => ['priority' => 3],
                'SlmQueue\Strategy\FinalStrategy' => ['foo' => 'bar'],
                'SlmQueue\Strategy\SomeStrategy' => 'not_an_array',
            ]]);

        $property = $class->getProperty('pluginManager');
        $property->setAccessible(true);

        $pluginManagerMock = $property->getValue($this->listener);

        $pluginManagerMock->expects($this->at(0))->method('get')->with('SlmQueue\Strategy\SomeStrategy', []);
        $pluginManagerMock->expects($this->at(1))->method('get')->with('SlmQueue\Strategy\OtherStrategy', []); // priority is removed
        $pluginManagerMock->expects($this->at(2))->method('get')->with('SlmQueue\Strategy\FinalStrategy', ['foo' => 'bar']);

        $strategyMock          = $this->getMock('SlmQueue\Strategy\AbstractStrategy');

        $eventManagerMock->expects($this->at(1))->method('attachAggregate')->with($strategyMock);
        $eventManagerMock->expects($this->at(2))->method('attachAggregate')->with($strategyMock, 3);
        $eventManagerMock->expects($this->at(3))->method('attachAggregate')->with($strategyMock);

        $this->listener->attachQueueListeners($this->event);
    }

    public function testAttachQueueListenersThrowsExceptionWhenNoListenersHaveBeenAttachedListeningToWorkerEventProcess()
    {
        $workerMock       = $this->event->getTarget();
        $eventManagerMock = $workerMock->getEventManager();
        $eventManagerMock->expects($this->any())->method('getEvents')->will($this->returnValue([WorkerEvent::EVENT_PROCESS_IDLE]));

        $this->setExpectedException('SlmQueue\Exception\RunTimeException');
        $this->listener->attachQueueListeners($this->event);
    }
}
