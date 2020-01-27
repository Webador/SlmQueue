<?php

namespace SlmQueueTest\Controller;

use PHPUnit\Framework\TestCase as TestCase;
use SlmQueueTest\Asset\FailingJob;
use SlmQueueTest\Asset\SimpleController;
use SlmQueueTest\Asset\SimpleJob;
use SlmQueueTest\Asset\SimpleQueue;
use SlmQueueTest\Asset\SimpleQueueFactory;
use SlmQueueTest\Asset\SimpleWorker;
use SlmQueue\Controller\Exception\WorkerProcessException;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueue\Strategy\MaxRunsStrategy;
use SlmQueue\Strategy\ProcessQueueStrategy;
use Laminas\Mvc\Router\RouteMatch;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\ServiceManager;

class AbstractControllerTest extends TestCase
{
    /**
     * @var QueuePluginManager
     */
    protected $queuePluginManager;

    /**
     * @var SimpleController
     */
    protected $controller;


    public function setUp(): void
    {
        $worker = new SimpleWorker();

        $eventManager = $worker->getEventManager();
        (new ProcessQueueStrategy())->attach($eventManager);
        (new MaxRunsStrategy(['max_runs' => 1]))->attach($eventManager);
        $serviceManager = new ServiceManager();
        $config = [
            'factories' => [
                'knownQueue' => SimpleQueueFactory::class,
            ],
        ];

        $this->queuePluginManager = new QueuePluginManager($serviceManager, $config);
        $this->controller         = new SimpleController($worker, $this->queuePluginManager);
    }

    public function testThrowExceptionIfQueueIsUnknown()
    {
        $routeMatch = new RouteMatch(['queue' => 'unknownQueue']);
        $this->controller->getEvent()->setRouteMatch($routeMatch);

        $this->expectException(ServiceNotFoundException::class);
        $this->controller->processAction();
    }

    public function testSimpleJob()
    {
        /** @var SimpleQueue $queue */
        $queue = $this->queuePluginManager->get('knownQueue');
        $queue->push(new SimpleJob());

        $routeMatch = new RouteMatch(['queue' => 'knownQueue']);
        $this->controller->getEvent()->setRouteMatch($routeMatch);

        $result = $this->controller->processAction();
        static::assertStringContainsString("Finished worker for queue 'knownQueue'", $result);
        static::assertStringContainsString("maximum of 1 jobs processed", $result);
    }

    public function testFailingJobThrowException()
    {
        /** @var SimpleQueue $queue */
        $queue = $this->queuePluginManager->get('knownQueue');
        $queue->push(new FailingJob());

        $routeMatch = new RouteMatch(['queue' => 'knownQueue']);
        $this->controller->getEvent()->setRouteMatch($routeMatch);

        $this->expectException(WorkerProcessException::class);
        $this->controller->processAction();
    }
}
