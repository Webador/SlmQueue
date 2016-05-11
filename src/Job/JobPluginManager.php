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
    public function get($name, $options = [], $usePeeringServiceManagers = true)
    {
        // parent::get calls validatePlugin() so we're sure $instance is a JobInterface
        $instance = parent::get($name, $options, $usePeeringServiceManagers);
        $instance->setMetadata('__name__', $name);

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($instance)
    {
        if ($instance instanceof JobInterface) {
            return; // we're okay
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement SlmQueue\Job\JobInterface',
            (is_object($instance) ? get_class($instance) : gettype($instance))
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function validatePlugin($plugin)
    {
        return $this->validate($plugin);
    }
}

