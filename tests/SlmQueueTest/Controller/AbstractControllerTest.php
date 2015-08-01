<?php

namespace SlmQueueTest\Controller;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueue\Strategy\MaxRunsStrategy;
use SlmQueue\Strategy\ProcessQueueStrategy;
use SlmQueue\Worker\WorkerEvent;
use SlmQueueTest\Asset\FailingJob;
use SlmQueueTest\Asset\SimpleController;
use SlmQueueTest\Asset\SimpleJob;
use SlmQueueTest\Asset\SimpleQueue;
use SlmQueueTest\Asset\SimpleWorker;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\Config;

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


    public function setUp()
    {
        $worker = new SimpleWorker();

        $worker->getEventManager()->attachAggregate(new ProcessQueueStrategy());
        $worker->getEventManager()->attachAggregate(new MaxRunsStrategy(['max_runs' => 1]));
        $config = new Config([
            'factories' => [
                'knownQueue' => 'SlmQueueTest\Asset\SimpleQueueFactory'
            ],
        ]);

        $this->queuePluginManager = new QueuePluginManager($config);
        $this->controller         = new SimpleController($worker, $this->queuePluginManager);
    }

    public function testThrowExceptionIfQueueIsUnknown()
    {
        $routeMatch = new RouteMatch(['queue' => 'unknownQueue']);
        $this->controller->getEvent()->setRouteMatch($routeMatch);

        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotFoundException');
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
        $this->assertContains("Finished worker for queue 'knownQueue'", $result);
        $this->assertContains("maximum of 1 jobs processed", $result);
    }

    public function testFailingJobThrowException()
    {
        /** @var SimpleQueue $queue */
        $queue = $this->queuePluginManager->get('knownQueue');
        $queue->push(new FailingJob());

        $routeMatch = new RouteMatch(['queue' => 'knownQueue']);
        $this->controller->getEvent()->setRouteMatch($routeMatch);

        $this->setExpectedException('SlmQueue\Controller\Exception\WorkerProcessException');
        $this->controller->processAction();
    }
}
