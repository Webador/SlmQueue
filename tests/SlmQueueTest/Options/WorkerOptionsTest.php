<?php

namespace SlmQueueTest\Options;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueueTest\Util\ServiceManagerFactory;
use Zend\ServiceManager\ServiceManager;

class WorkerOptionsTest extends TestCase
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

    public function testCreateWorkerOptions()
    {
        /** @var $workerOptions \SlmQueue\Options\WorkerOptions */
        $workerOptions = $this->serviceManager->get('SlmQueue\Options\WorkerOptions');

        $this->assertInstanceOf('SlmQueue\Options\WorkerOptions', $workerOptions);
        $this->assertEquals(100000, $workerOptions->getMaxRuns());
        $this->assertEquals(104857600, $workerOptions->getMaxMemory());
    }
}
