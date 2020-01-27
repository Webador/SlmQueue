<?php

namespace SlmQueueTest\Job;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use SlmQueue\Job\Exception\RuntimeException;
use SlmQueue\Job\JobPluginManager;
use SlmQueueTest\Asset\SimpleJob;
use SlmQueueTest\Util\ServiceManagerFactory;

class JobPluginManagerTest extends TestCase
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

    public function testCanRetrievePluginManagerWithServiceManager(): void
    {
        $jobPluginManager = $this->serviceManager->get(JobPluginManager::class);
        static::assertInstanceOf(JobPluginManager::class, $jobPluginManager);
    }

    public function testAskingTwiceForTheSameJobReturnsDifferentInstances(): void
    {
        $jobPluginManager = $this->serviceManager->get(JobPluginManager::class);

        $firstInstance = $jobPluginManager->get(SimpleJob::class);
        $secondInstance = $jobPluginManager->get(SimpleJob::class);

        static::assertNotSame($firstInstance, $secondInstance);
    }

    public function testPluginManagerSetsServiceNameAsMetadata(): void
    {
        $serviceManager = new ServiceManager();
        $jobPluginManager = new JobPluginManager($serviceManager);
        $jobPluginManager->setInvokableClass('SimpleJob', SimpleJob::class);

        $instance = $jobPluginManager->get('SimpleJob');

        static::assertInstanceOf(SimpleJob::class, $instance);
        static::assertEquals('SimpleJob', $instance->getMetadata('__name__'));
    }

    public function testPluginManagerThrowsExceptionOnInvalidJobClasses(): void
    {
        $serviceManager = new ServiceManager();
        $jobPluginManager = new JobPluginManager($serviceManager);
        $jobPluginManager->setInvokableClass('InvalidJob', 'stdClass');

        $this->expectException(RuntimeException::class);

        $instance = $jobPluginManager->get('InvalidJob');
    }
}
