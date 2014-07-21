<?php

namespace SlmQueueTest\Factory;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Factory\QueueControllerPluginFactory;
use SlmQueueTest\Util\ServiceManagerFactory;

class QueueControllerPluginFactoryTest extends TestCase
{

    public function testCreateService()
    {
        $sm      = ServiceManagerFactory::getServiceManager();
        $factory = new QueueControllerPluginFactory();
        $queueControllerPluginFactory  = $factory->createService($sm);

        $this->assertInstanceOf('SlmQueue\Controller\Plugin\QueuePlugin', $queueControllerPluginFactory);
    }

}
