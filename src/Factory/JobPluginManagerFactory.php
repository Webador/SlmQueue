<?php

namespace SlmQueue\Factory;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerInterface;
use SlmQueue\Job\JobPluginManager;

class JobPluginManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): JobPluginManager
    {
        // We do not need to check if jobs is an empty array because every the JobPluginManager automatically
        // adds invokables if the job name is not known, which will be sufficient most of the time
        $config = $container->get('config');
        $config = $config['slm_queue']['job_manager'];

        return new JobPluginManager($container, $config);
    }

    public function createService(ServiceLocatorInterface $serviceLocator): JobPluginManager
    {
        return $this($serviceLocator, JobPluginManager::class);
    }
}
