<?php

namespace SlmQueue\Job;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * JobPluginManager
 * 
 * @method JobInterface get($name)
 */
class JobPluginManager extends AbstractPluginManager
{
    /**
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * @param  mixed $plugin
     * @throws Exception\RuntimeException
     * @return void
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof JobInterface) {
            return; // we're okay
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement SlmQueue\Job\JobInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
