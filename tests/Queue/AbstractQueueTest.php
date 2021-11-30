<?php

namespace SlmQueueTest\Queue;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use SlmQueueTest\Asset\SimpleQueue;
use SlmQueueTest\Asset\SimpleWorker;
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

    public function testGetters(): void
    {
        $jobPluginManager = $this->serviceManager->get('SlmQueue\Job\JobPluginManager');
        $workerPluginManager = $this->serviceManager->get('SlmQueue\Worker\WorkerPluginManager');

        $queue = new SimpleQueue('name', $jobPluginManager, $workerPluginManager);

        $this->assertSame('name', $queue->getName());
        $this->assertSame($jobPluginManager, $queue->getJobPluginManager());
        $this->assertInstanceOf(SimpleWorker::class, $queue->getWorker());
    }
}
