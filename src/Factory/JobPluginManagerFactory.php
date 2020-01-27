<?php

namespace SlmQueue\Factory;

use SlmQueue\Job\JobPluginManager;
use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * JobPluginManagerFactory
 */
class JobPluginManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // We do not need to check if jobs is an empty array because every the JobPluginManager automatically
        // adds invokables if the job name is not known, which will be sufficient most of the time
        $config = $container->get('config');
        $config = $config['slm_queue']['job_manager'];
        $jobPluginManager = new JobPluginManager($container, $config);

        return $jobPluginManager;
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, JobPluginManager::class);
    }
}
