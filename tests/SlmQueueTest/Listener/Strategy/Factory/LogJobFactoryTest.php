<?php

namespace SlmQueueTest\Listener\Strategy\Factory;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Listener\Strategy\Factory\LogJobStrategyFactory;
use SlmQueueTest\Util\ServiceManagerFactory;

class LogJobFactoryTest extends TestCase
{

    public function testCreateService()
    {
        $sm       = ServiceManagerFactory::getServiceManager();
        $factory  = new LogJobStrategyFactory();
        $strategy = $factory->createService($sm);

        $this->assertInstanceOf('SlmQueue\Listener\Strategy\LogJobStrategy', $strategy);
    }

}
