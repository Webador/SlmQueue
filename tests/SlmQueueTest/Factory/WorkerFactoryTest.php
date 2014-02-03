<?php

namespace SlmQueueTest\Factory;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Factory\WorkerFactory;
use SlmQueueTest\Util\ServiceManagerFactory;

class AbstractWorkerTest extends TestCase
{
    public function testCreateViaServiceManager()
    {
        $sm      = ServiceManagerFactory::getServiceManager();
        $worker  = $sm->get('SlmQueueTest\Asset\SimpleWorker');

        $this->assertInstanceOf('SlmQueueTest\Asset\SimpleWorker', $worker);
    }

    public function testCreateService()
    {
        $sm      = ServiceManagerFactory::getServiceManager();
        $factory = new WorkerFactory();
        $worker  = $factory->createService($sm, 'slmqueuetestassetsimpleworker', 'SlmQueueTest\Asset\SimpleWorker');

        $this->assertInstanceOf('SlmQueueTest\Asset\SimpleWorker', $worker);
    }

}
