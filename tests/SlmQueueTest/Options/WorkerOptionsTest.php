<?php

namespace SlmQueueTest\Options;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Options\WorkerOptions;

class WorkerOptionsTest extends TestCase
{
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
