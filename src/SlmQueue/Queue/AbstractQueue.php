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
    public function getJobPluginManager()
    {
        return $this->jobPluginManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->getName();
    }
}
