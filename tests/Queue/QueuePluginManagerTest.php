<?php

namespace SlmQueueTest\Queue;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueue\Queue\Exception\RuntimeException;
use SlmQueueTest\Util\ServiceManagerFactory;
use Laminas\ServiceManager\ServiceManager;

class QueuePluginManagerTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    public function setUp()
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

        $firstInstance  = $queuePluginManager->get('basic-queue');
        $secondInstance = $queuePluginManager->get('basic-queue');

        static::assertSame($firstInstance, $secondInstance);
    }

    public function testPluginValidation()
    {
        $serviceManager = new ServiceManager();
        $manager = new QueuePluginManager($serviceManager);
        $queue   = new \stdClass();

        $this->setExpectedException(RuntimeException::class);
        $manager->validatePlugin($queue);
    }
}
