<?php

namespace SlmQueueTest\Queue;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueueTest\Asset\SimpleQueue;
use SlmQueueTest\Util\ServiceManagerFactory;
use Zend\ServiceManager\ServiceManager;

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

        $this->assertSame('name', $queue->getName());
        $this->assertSame($jobPluginManager, $queue->getJobPluginManager());
    }

}
