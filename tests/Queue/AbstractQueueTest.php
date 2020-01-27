<?php

namespace SlmQueueTest\Queue;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase as TestCase;
use SlmQueueTest\Asset\SimpleQueue;
use SlmQueueTest\Util\ServiceManagerFactory;

class AbstractQueueTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->serviceManager = ServiceManagerFactory::getServiceManager();
    }

    public function testGetters()
    {
        $jobPluginManager = $this->serviceManager->get('SlmQueue\Job\JobPluginManager');

        $queue = new SimpleQueue('name', $jobPluginManager);

        static::assertSame('name', $queue->getName());
        static::assertSame($jobPluginManager, $queue->getJobPluginManager());
    }
}
