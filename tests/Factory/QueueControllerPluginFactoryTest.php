<?php

namespace SlmQueueTest\Factory;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Factory\QueueControllerPluginFactory;
use SlmQueueTest\Util\ServiceManagerFactory;

class QueueControllerPluginFactoryTest extends TestCase
{

    public function testCreateService()
    {
        $serviceManager               = ServiceManagerFactory::getServiceManager();
        $controllerPluginManager      = $serviceManager->get('ControllerPluginManager');
        $factory                      = new QueueControllerPluginFactory();
        $queueControllerPluginFactory = $factory->createService($controllerPluginManager);

        $this->assertInstanceOf('SlmQueue\Controller\Plugin\QueuePlugin', $queueControllerPluginFactory);
    }
}
