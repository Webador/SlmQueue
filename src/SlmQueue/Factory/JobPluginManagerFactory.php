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
        // We do not need to check if jobs is an empty array because every the JobPluginManager automatically
        // adds invokables if the job name is not known, which will be sufficient most of the time
        $config = $serviceLocator->get('Config');
        $config = $config['slm_queue']['job_manager'];

        $jobPluginManager = new JobPluginManager(new Config($config));
        $jobPluginManager->setServiceLocator($serviceLocator);

        return $jobPluginManager;
    }
}
