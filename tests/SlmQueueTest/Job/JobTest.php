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

        $this->assertEquals(3, $job->getMetadata('id'));
    }

    public function testCorrectlySerializeJob()
    {
        $job = new SimpleJob();
        $job->setContent('Foo');

        $this->assertEquals('{"class":"SlmQueueTest\\\Asset\\\SimpleJob","content":"Foo"}', json_encode($job));
    }

    public function testCorrectlyUnserializeJob()
    {
        $job = new SimpleJob();
        $job->setContent('Foo');
        $job = json_decode(json_encode($job), true);

        $this->assertEquals('SlmQueueTest\Asset\SimpleJob', $job['class']);
        $this->assertEquals('Foo', $job['content']);
    }

    public function testJobCanBeExecuted()
    {
        // The simple Job just add a metadata
        $job = new SimpleJob();
        $job->execute();

        $this->assertEquals('bar', $job->getMetadata('foo'));
    }
}
