<?php

namespace SlmQueueTest\Job;

use PHPUnit\Framework\TestCase;
use SlmQueueTest\Asset\JobWithDependencies;
use SlmQueueTest\Asset\SimpleJob;

class JobTest extends TestCase
{
    public function testSetIdAutomaticallyAddMetadata(): void
    {
        $job = new SimpleJob();
        $job->setId(3);

        static::assertEquals(3, $job->getId());
        static::assertEquals(3, $job->getMetadata('__id__'));
    }

    public function testJobCanBeExecuted(): void
    {
        // The simple Job just add a metadata
        $job = new SimpleJob();
        $job->execute();

        static::assertEquals('bar', $job->getMetadata('foo'));
    }

    public function testInternalFactoryMethod(): void
    {
        $job = JobWithDependencies::testInternalFactoryMethod();

        $this->assertInstanceOf(JobWithDependencies::class, $job);
        $this->assertEquals(['a' => 123], $job->getContent());
    }
}
