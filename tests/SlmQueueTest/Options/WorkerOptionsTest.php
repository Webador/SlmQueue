<?php

namespace SlmQueueTest\Options;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Options\WorkerOptions;
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

    public function testCanRetrieveWorkerOptionsWithServiceManager()
    {
        $workerOptions = $this->serviceManager->get('SlmQueue\Options\WorkerOptions');
        $this->assertInstanceOf('SlmQueue\Options\WorkerOptions', $workerOptions);
    }

    public function testGettersAndSetters()
    {
        $workerOptions = new WorkerOptions(array(
            'max_runs'   => 10,
            'max_memory' => 1000
        ));

        $this->assertInstanceOf('SlmQueue\Options\WorkerOptions', $workerOptions);
        $this->assertEquals(10, $workerOptions->getMaxRuns());
        $this->assertEquals(1000, $workerOptions->getMaxMemory());
    }
}
