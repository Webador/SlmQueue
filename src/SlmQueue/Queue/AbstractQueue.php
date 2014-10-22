<?php

namespace SlmQueue\Queue;

use SlmQueue\Job\JobInterface;
use SlmQueue\Job\JobPluginManager;

/**
 * AbstractQueue
 */
abstract class AbstractQueue implements QueueInterface
{
    /**
     * @var JobPluginManager
     */
    protected $jobPluginManager;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param string           $name
     * @param JobPluginManager $jobPluginManager
     */
    public function __construct($name, JobPluginManager $jobPluginManager)
    {
        $this->name             = $name;
        $this->jobPluginManager = $jobPluginManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getJobPluginManager()
    {
        return $this->jobPluginManager;
    }

    /**
     * Create a job instance based on serialized input
     *
     * Instantiate a job based on a serialized data string. The string
     * is a JSON string containing job name, content and metadata. Use
     * the decoded JSON value to create a job instance, configure it
     * and return it.
     *
     * @param  string $string
     * @param  array  $metadata
     * @return \SlmQueue\Job\JobInterface
     */
    public function unserializeJob($string, array $metadata = array())
    {
        $data     =  json_decode($string, true);
        $name     =  $data['metadata']['__name__'];
        $metadata += $data['metadata'];
        $content  =  unserialize($data['content']);

        if (isset($data['metadata']['__jobchain__'])) {
            $self = $this; // needed for php5.3 (?)
            $jobChain = array_map(function($chainedJob) use ($self) {
                    return $self->unserializeJob($chainedJob);
                }, $data['metadata']['__jobchain__']);
            $metadata['__jobchain__'] = $jobChain;
        }

        /** @var $job \SlmQueue\Job\JobInterface */
        $job = $this->getJobPluginManager()->get($name);

        $job->setContent($content);
        $job->setMetadata($metadata);

        if ($job instanceof QueueAwareInterface) {
            $job->setQueue($this);
        }

        return $job;
    }

    /**
     * Serialize job to allow persistence
     *
     * The serialization format is a JSON object with keys "content",
     * "metadata" and "__name__". When a job is fetched from the SL, a job name
     * will be set and be available as metadata. An invokable job has no service
     * name and therefore the FQCN will be used.
     *
     * @param  JobInterface $job The job to persist
     * @return string
     */
    public function serializeJob(JobInterface $job)
    {
        $job->setMetadata('__name__', $job->getMetadata('__name__', get_class($job)));

        $self = $this; // needed for php5.3 (?)
        if ($jobChain = $job->getMetadata('__jobchain__', false)) {
            $serializedJobChain = array_map(function($chainedJob) use ($self) {
                    return $self->serializeJob($chainedJob);
                }, $jobChain);
            $job->setMetadata('__jobchain__', $serializedJobChain);
        }

        $data = array(
            'content'  => serialize($job->getContent()),
            'metadata' => $job->getMetadata()
        );

        return json_encode($data);
    }
}
