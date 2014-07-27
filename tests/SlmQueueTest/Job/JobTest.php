<?php

namespace SlmQueueTest\Job;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueueTest\Asset\SimpleJob;

class JobTest extends TestCase
{
    public function testSetIdAutomaticallyAddMetadata()
    {
        $job = new SimpleJob();
        $job->setId(3);

        $this->assertEquals(3, $job->getId());
        $this->assertEquals(3, $job->getMetadata('id'));
    }

    public function testJobCanBeExecuted()
    {
        // The simple Job just add a metadata
        $job = new SimpleJob();
        $job->execute();

        $this->assertEquals('bar', $job->getMetadata('foo'));
    }
}
