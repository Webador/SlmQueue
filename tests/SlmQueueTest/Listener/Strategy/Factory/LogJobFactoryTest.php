<?php

namespace SlmQueueTest\Listener\Strategy\Factory;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Listener\Strategy\Factory\LogJobStrategyFactory;
use SlmQueue\Listener\StrategyPluginManager;
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

        $this->assertInstanceOf('SlmQueue\Listener\Strategy\LogJobStrategy', $strategy);
    }

}
