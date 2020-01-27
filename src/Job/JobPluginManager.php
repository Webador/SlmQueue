<?php

namespace SlmQueue\Job;

use SlmQueue\ServiceManager\AbstractPluginManager;

class JobPluginManager extends AbstractPluginManager
{
    /**
     * SM2
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * SM3
     *
     * @var bool
     */
    protected $sharedByDefault = false;

    /**
     * @inheritdoc
     *
     * @param string $name
     * @param array  $options
     * @param bool   $usePeeringServiceManagers
     * @return JobInterface
     */
    public function get($name, $options = [], $usePeeringServiceManagers = true): JobInterface
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
}
