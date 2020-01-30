<?php

namespace SlmQueueTest\Factory;

use PHPUnit\Framework\TestCase;
use SlmQueue\Factory\WorkerFactory;
use SlmQueueTest\Util\ServiceManagerFactory;

class AbstractWorkerTest extends TestCase
{
    public function testCreateViaServiceManager(): void
    {
        $sm = ServiceManagerFactory::getServiceManager();
        $worker = $sm->get('SlmQueueTest\Asset\SimpleWorker');

        static::assertInstanceOf('SlmQueueTest\Asset\SimpleWorker', $worker);
    }

    public function testCreateService(): void
    {
        $sm = ServiceManagerFactory::getServiceManager();
        $factory = new WorkerFactory();
        $worker = $factory->__invoke($sm, 'SlmQueueTest\Asset\SimpleWorker');

        static::assertInstanceOf('SlmQueueTest\Asset\SimpleWorker', $worker);
    }
}
