<?php

namespace SlmQueueTest\Job;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueueTest\Asset\SimpleJob;
use SlmQueueTest\Util\ServiceManagerFactory;
use SlmQueue\Job\Exception\RuntimeException;
use SlmQueue\Job\JobPluginManager;
use Laminas\ServiceManager\ServiceManager;

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
        $jobPluginManager = $this->serviceManager->get(JobPluginManager::class);
        static::assertInstanceOf(JobPluginManager::class, $jobPluginManager);
    }

    public function testAskingTwiceForTheSameJobReturnsDifferentInstances()
    {
        $jobPluginManager = $this->serviceManager->get(JobPluginManager::class);

        $firstInstance  = $jobPluginManager->get(SimpleJob::class);
        $secondInstance = $jobPluginManager->get(SimpleJob::class);

        static::assertNotSame($firstInstance, $secondInstance);
    }

    public function testPluginManagerSetsServiceNameAsMetadata()
    {
        $serviceManager = new ServiceManager();
        $jobPluginManager = new JobPluginManager($serviceManager);
        $jobPluginManager->setInvokableClass('SimpleJob', SimpleJob::class);

        $instance = $jobPluginManager->get('SimpleJob');

        static::assertInstanceOf(SimpleJob::class, $instance);
        static::assertEquals('SimpleJob', $instance->getMetadata('__name__'));
    }

    public function testPluginManagerThrowsExceptionOnInvalidJobClasses()
    {
        $serviceManager = new ServiceManager();
        $jobPluginManager = new JobPluginManager($serviceManager);
        $jobPluginManager->setInvokableClass('InvalidJob', 'stdClass');

        $this->setExpectedException(RuntimeException::class);

        $instance = $jobPluginManager->get('InvalidJob');
    }
}
