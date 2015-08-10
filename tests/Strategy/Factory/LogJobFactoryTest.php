<?php

namespace SlmQueueTest\Strategy\Factory;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Strategy\Factory\LogJobStrategyFactory;
use SlmQueue\Strategy\StrategyPluginManager;
use SlmQueueTest\Util\ServiceManagerFactory;

class LogJobFactoryTest extends TestCase
{

    public function testCreateService()
    {
        $plugin   = new StrategyPluginManager();
        $sm       = ServiceManagerFactory::getServiceManager();

        $plugin->setServiceLocator($sm);

        $factory  = new LogJobStrategyFactory();
        $strategy = $factory->createService($plugin);

        $this->assertInstanceOf('SlmQueue\Strategy\LogJobStrategy', $strategy);
    }

}
