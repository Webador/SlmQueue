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

    public function testCorrectlySerializeJobContent()
    {
        $job = new SimpleJob();
        $job->setContent('Foo');

        $this->assertEquals('{"class":"SlmQueueTest\\\Asset\\\SimpleJob","content":"Foo","metadata":[]}', $job->jsonSerialize());
    }

    public function testCorrectlySerializeJobMetadata()
    {
        $job = new SimpleJob();
        $job->setMetadata('foo', 'Bar');

        $this->assertEquals('{"class":"SlmQueueTest\\\Asset\\\SimpleJob","content":null,"metadata":{"foo":"Bar"}}', $job->jsonSerialize());
    }

    public function testCorrectlySerializeJobContentAndMetadata()
    {
        $job = new SimpleJob();
        $job->setContent('Foo');
        $job->setMetadata('foo', 'Bar');

        $this->assertEquals('{"class":"SlmQueueTest\\\Asset\\\SimpleJob","content":"Foo","metadata":{"foo":"Bar"}}', $job->jsonSerialize());
    }

    public function testCorrectlyUnserializeJob()
    {
        $job = new SimpleJob();
        $job->setContent('Foo');
        $job = json_decode($job->jsonSerialize(), true);

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
