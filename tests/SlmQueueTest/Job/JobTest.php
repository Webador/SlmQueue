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
        $this->assertEquals(3, $job->getMetadata('__id__'));
    }

    public function testJobCanBeExecuted()
    {
        // The simple Job just add a metadata
        $job = new SimpleJob();
        $job->execute();

        $this->assertEquals('bar', $job->getMetadata('foo'));
    }

    public function testJobsCanContainChainedJobs()
    {
        $mainJob = new SimpleJob();

        $dependentJob = new SimpleJob();

        $mainJob->chainJob($dependentJob);

        $this->assertContains($dependentJob, $mainJob->getMetadata('__jobchain__', array()));
    }
}
