<?php

namespace SlmQueue\Factory;

use SlmQueue\Job\JobPluginManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * JobPluginManagerFactory
 */
class JobPluginManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // We do not need to check if jobs is empty array because every the JobPluginManager automatically
        // adds invokables if the job name is not known, which will be sufficient most of the time
        $config = $serviceLocator->get('Config')['slm_queues']['jobs'];
        return new JobPluginManager(new Config($config));
    }
}
