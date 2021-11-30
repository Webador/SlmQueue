<?php

namespace SlmQueueTest\Factory;

use PHPUnit\Framework\TestCase;
use SlmQueue\Factory\WorkerAbstractFactory;
use SlmQueueTest\Util\ServiceManagerFactory;

class AbstractWorkerTest extends TestCase
{
    public function testCreateService(): void
    {
        $sm = ServiceManagerFactory::getServiceManager();
        $factory = new WorkerAbstractFactory();
        $worker = $factory->__invoke($sm, 'SlmQueueTest\Asset\SimpleWorker');

        static::assertInstanceOf('SlmQueueTest\Asset\SimpleWorker', $worker);
    }
}
