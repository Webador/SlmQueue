<?php

namespace SlmQueueTest\Queue;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueueTest\Asset\SimpleQueue;
use SlmQueueTest\Util\ServiceManagerFactory;
use Laminas\ServiceManager\ServiceManager;

class AbstractQueueTest extends TestCase
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

    public function testGetters()
    {
        $jobPluginManager = $this->serviceManager->get('SlmQueue\Job\JobPluginManager');

        $queue = new SimpleQueue('name', $jobPluginManager);

        static::assertSame('name', $queue->getName());
        static::assertSame($jobPluginManager, $queue->getJobPluginManager());
    }

}
