<?php

namespace SlmQueueTest\Factory;

use PHPUnit\Framework\TestCase as TestCase;
use SlmQueue\Controller\Plugin\QueuePlugin;
use SlmQueue\Factory\QueueControllerPluginFactory;
use SlmQueueTest\Util\ServiceManagerFactory;

class QueueControllerPluginFactoryTest extends TestCase
{

    public function testCreateService()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();
        $factory = new QueueControllerPluginFactory();

        $queueControllerPluginFactory = $factory($serviceManager, QueuePlugin::class);
        static::assertInstanceOf(QueuePlugin::class, $queueControllerPluginFactory);
    }
}
