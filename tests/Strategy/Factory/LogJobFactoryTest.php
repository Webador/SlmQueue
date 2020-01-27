<?php

namespace SlmQueueTest\Strategy\Factory;

use PHPUnit\Framework\TestCase as TestCase;
use SlmQueue\Strategy\Factory\LogJobStrategyFactory;
use SlmQueue\Strategy\LogJobStrategy;
use SlmQueueTest\Util\ServiceManagerFactory;

class LogJobFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();
        $factory = new LogJobStrategyFactory();
        $strategy = $factory($serviceManager, LogJobStrategy::class);

        static::assertInstanceOf(LogJobStrategy::class, $strategy);
    }
}
