<?php

namespace SlmQueueTest\Queue;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Queue\QueuePluginManager;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;

class QueuePluginManagerTest extends TestCase
{
    /**
     * @var QueuePluginManager
     */
    protected $queuePluginManager;

    public function setUp()
    {
        parent::setUp();
        $this->queuePluginManager = new QueuePluginManager();
    }

    public function testCanRetrievePluginManagerWithServiceManager()
    {
        $serviceManager = new ServiceManager(
            new ServiceManagerConfig(array(
                'factories' => array(
                    'QueuePluginManager' => 'SlmQueue\Factory\QueuePluginManagerFactory',
                ),
            ))
        );
        $serviceManager->setService('Config', include __DIR__ . '/../../testing.config.php');

        $queuePluginManager = $serviceManager->get('QueuePluginManager');
        $this->assertInstanceOf('SlmQueue\Queue\QueuePluginManager', $queuePluginManager);
    }

    public function testAskingTwiceTheSameQueueReturnsTheSameInstance()
    {
        // TODO
    }
}
