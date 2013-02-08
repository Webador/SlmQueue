<?php

namespace SlmQueue\Queue;

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
        return $this->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function getJobPluginManager()
    {
        return $this->jobPluginManager;
    }

    /**
     * Create a job from the serialized data extracted from the queue. The metadata (id...) is then injected to the
     * job. Note that this follow the way a \SlmQueue\Job\AbstractJob is serialized. If you do fancy stuff, you may
     * need to override the queue too so that the job is created the right way
     *
     * @param  string $jsonData
     * @param  array  $metadata
     * @return \SlmQueue\Job\JobInterface
     */
    protected function createJob($jsonData, array $metadata = array())
    {
        $data = json_decode($jsonData, true);

        /** @var $job \SlmQueue\Job\JobInterface */
        $job = $this->jobPluginManager->create($data['class']);
        $job->setMetadata($metadata)
            ->setContent($data['content']);

        return $job;
    }
}
