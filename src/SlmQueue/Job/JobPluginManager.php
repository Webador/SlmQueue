<?php

namespace SlmQueue\Job;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * JobPluginManager
 */
class JobPluginManager extends AbstractPluginManager
{
    /**
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * @param  mixed $plugin
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof JobInterface) {
            return; // we're okay
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must implement SlmQueue\Job\JobInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
