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
     * @var object
     */
    protected $options;


    /**
     * @param JobPluginManager $jobPluginManager
     * @param string           $name
     * @param object|null      $options
     */
    public function __construct(JobPluginManager $jobPluginManager, $name, $options = null)
    {
        $this->jobPluginManager = $jobPluginManager;
        $this->name             = $name;

        if ($this->options !== null) {
            $this->options = $options;
        }
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

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return $this->options;
    }
}
