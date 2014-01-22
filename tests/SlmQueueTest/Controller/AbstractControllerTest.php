<?php

namespace SlmQueueTest\Controller;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Options\WorkerOptions;
use SlmQueue\Queue\QueuePluginManager;
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
        $this->queue = new QueuePluginManager(new Config(array('factories' => array('knownQueue' => 'SlmQueueTest\Asset\SimpleQueueFactory'))));
        $this->controller = new SimpleController(new SimpleWorker($this->queue, new WorkerOptions()));
    }

    public function testThrowExceptionIfQueueIsUnknown()
    {
        $routeMatch = new RouteMatch(array('queue' => 'unknownQueue'));
        $this->controller->getEvent()->setRouteMatch($routeMatch);

        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotFoundException');
        $this->controller->processAction();
    }

    public function testSimpleJob()
    {
        /** @var SimpleQueue $queue */
        $queue = $this->queue->get('knownQueue');
        $queue->push(new SimpleJob());

        $routeMatch = new RouteMatch(array('queue' => 'knownQueue'));
        $this->controller->getEvent()->setRouteMatch($routeMatch);

        $this->assertContains("Finished worker for queue 'knownQueue' with 1 jobs", $this->controller->processAction());
    }

    public function testFailingJobThrowException()
    {
        /** @var SimpleQueue $queue */
        $queue = $this->queue->get('knownQueue');
        $queue->push(new FailingJob());

        $routeMatch = new RouteMatch(array('queue' => 'knownQueue'));
        $this->controller->getEvent()->setRouteMatch($routeMatch);

        $this->setExpectedException('SlmQueue\Controller\Exception\WorkerProcessException');
        $this->controller->processAction();
    }
}
