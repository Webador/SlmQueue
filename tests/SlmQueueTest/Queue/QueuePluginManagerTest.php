<?php

namespace SlmQueueTest\Queue;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueueTest\Util\ServiceManagerFactory;
use Zend\ServiceManager\ServiceManager;

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
        $queuePluginManager = $this->serviceManager->get('SlmQueue\Queue\QueuePluginManager');
        $this->assertInstanceOf('SlmQueue\Queue\QueuePluginManager', $queuePluginManager);
    }

    public function testAskingTwiceTheSameQueueReturnsTheSameInstance()
    {
        $queuePluginManager = $this->serviceManager->get('SlmQueue\Queue\QueuePluginManager');

        $firstInstance  = $queuePluginManager->get('basic-queue');
        $secondInstance = $queuePluginManager->get('basic-queue');

        $this->assertSame($firstInstance, $secondInstance);
    }

    public function testPluginValidation()
    {
        $manager = new QueuePluginManager();
        $queue   = new \stdClass();

        $this->setExpectedException('SlmQueue\Queue\Exception\RuntimeException');
        $manager->validatePlugin($queue);
    }
}
