<?php

namespace SlmQueueTest\Options;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Options\ModuleOptions;

class ModuleOptionsTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $moduleOptions = new ModuleOptions(array(
            'worker' => array(
                'max_runs'   => 10,
                'max_memory' => 1000
            ),
            'queues' => array(
                'foo' => array()
            )
        ));

        $this->assertInstanceOf('SlmQueue\Options\WorkerOptions', $moduleOptions->getWorker());
        $this->assertEquals(10, $moduleOptions->getWorker()->getMaxRuns());
        $this->assertEquals(1000, $moduleOptions->getWorker()->getMaxMemory());

        $this->assertInternalType('array', $moduleOptions->getQueues());
    }
}
