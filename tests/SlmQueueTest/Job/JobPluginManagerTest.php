<?php

namespace SlmQueueTest\Job;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Job\JobPluginManager;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;

class JobPluginManagerTest extends TestCase
{
    /**
     * @var JobPluginManager
     */
    protected $jobPluginManager;

    public function setUp()
    {
        parent::setUp();
        $this->jobPluginManager = new JobPluginManager();
    }

    public function testCanRetrievePluginManagerWithServiceManager()
    {
        $serviceManager = new ServiceManager(
            new ServiceManagerConfig(array(
                'factories' => array(
                    'JobPluginManager' => 'SlmQueue\Factory\JobPluginManagerFactory',
                ),
            ))
        );
        $serviceManager->setService('Config', include __DIR__ . '/../../testing.config.php');

        $jobPluginManager = $serviceManager->get('JobPluginManager');
        $this->assertInstanceOf('SlmQueue\Job\JobPluginManager', $jobPluginManager);
    }

    public function testAskingTwiceForTheSameJobReturnsDifferentInstances()
    {
        // TODO
    }
}
