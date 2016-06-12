<?php

namespace SlmQueueTest\Factory;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Controller\Plugin\QueuePlugin;
use SlmQueue\Factory\QueueControllerPluginFactory;
use SlmQueueTest\Util\ServiceManagerFactory;

class QueueControllerPluginFactoryTest extends TestCase
{

    public function testCreateService()
    {
        $serviceManager               = ServiceManagerFactory::getServiceManager();
        $factory                      = new QueueControllerPluginFactory();

        $queueControllerPluginFactory = $factory($serviceManager, QueuePlugin::class);
        $this->assertInstanceOf(QueuePlugin::class, $queueControllerPluginFactory);
    }
}
