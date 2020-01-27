<?php

namespace SlmQueueTest\Queue;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase as TestCase;
use SlmQueue\Queue\Exception\RuntimeException;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueueTest\Util\ServiceManagerFactory;
use stdClass;

class QueuePluginManagerTest extends TestCase
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

    public function testCanRetrievePluginManagerWithServiceManager()
    {
        $queuePluginManager = $this->serviceManager->get(QueuePluginManager::class);
        static::assertInstanceOf(QueuePluginManager::class, $queuePluginManager);
    }

    public function testAskingTwiceTheSameQueueReturnsTheSameInstance()
    {
        $queuePluginManager = $this->serviceManager->get(QueuePluginManager::class);

        $firstInstance = $queuePluginManager->get('basic-queue');
        $secondInstance = $queuePluginManager->get('basic-queue');

        static::assertSame($firstInstance, $secondInstance);
    }

    public function testPluginValidation()
    {
        $serviceManager = new ServiceManager();
        $manager = new QueuePluginManager($serviceManager);
        $queue = new stdClass();

        $this->expectException(RuntimeException::class);
        $manager->validatePlugin($queue);
    }
}
