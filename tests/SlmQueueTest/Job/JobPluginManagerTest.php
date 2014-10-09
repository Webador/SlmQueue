<?php

namespace SlmQueueTest\Job;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Job\JobPluginManager;
use SlmQueueTest\Util\ServiceManagerFactory;
use Zend\ServiceManager\ServiceManager;

class JobPluginManagerTest extends TestCase
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
        $jobPluginManager = $this->serviceManager->get('SlmQueue\Job\JobPluginManager');
        $this->assertInstanceOf('SlmQueue\Job\JobPluginManager', $jobPluginManager);
    }

    public function testAskingTwiceForTheSameJobReturnsDifferentInstances()
    {
        $jobPluginManager = $this->serviceManager->get('SlmQueue\Job\JobPluginManager');

        $firstInstance  = $jobPluginManager->get('SlmQueueTest\Asset\SimpleJob');
        $secondInstance = $jobPluginManager->get('SlmQueueTest\Asset\SimpleJob');

        $this->assertNotSame($firstInstance, $secondInstance);
    }

    public function testPluginManagerSetsServiceNameAsMetadata()
    {
        $jobPluginManager = new JobPluginManager;
        $jobPluginManager->setInvokableClass('SimpleJob', 'SlmQueueTest\Asset\SimpleJob');

        $instance = $jobPluginManager->get('SimpleJob');

        $this->assertInstanceOf('SlmQueueTest\Asset\SimpleJob', $instance);
        $this->assertEquals('SimpleJob', $instance->getMetadata('__name__'));
    }

    public function testPluginManagerThrowsExceptionOnInvalidJobClasses()
    {
        $jobPluginManager = new JobPluginManager;
        $jobPluginManager->setInvokableClass('InvalidJob', 'stdClass');

        $this->setExpectedException('SlmQueue\Job\Exception\RuntimeException');

        $instance = $jobPluginManager->get('InvalidJob');
    }
}
