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
     * @inheritdoc
     *
     * @param string $name
     * @param array $options
     * @param bool $usePeeringServiceManagers
     * @return JobInterface
     */
    public function get($name, $options = array(), $usePeeringServiceManagers = true)
    {
        // parent::get calls validatePlugin() so we're sure $instance is a JobInterface
        $instance = parent::get($name, $options, $usePeeringServiceManagers);
        $instance->setMetadata('__name__', $name);
        
        return $instance;
    }

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
