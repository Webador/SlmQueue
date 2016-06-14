<?php

namespace SlmQueueTest\Strategy\Factory;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueueTest\Util\ServiceManagerFactory;
use SlmQueue\Strategy\Factory\LogJobStrategyFactory;
use SlmQueue\Strategy\LogJobStrategy;

class LogJobFactoryTest extends TestCase
{

    public function testCreateService()
    {
        $serviceManager       = ServiceManagerFactory::getServiceManager();

        $factory  = new LogJobStrategyFactory();
        $strategy = $factory($serviceManager, LogJobStrategy::class);

        $this->assertInstanceOf(LogJobStrategy::class, $strategy);
    }

}
