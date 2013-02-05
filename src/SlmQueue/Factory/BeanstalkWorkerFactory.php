<?php

namespace SlmQueue\Factory;

use SlmQueue\Options\WorkerOptions;
use SlmQueue\Worker\Beanstalk\Worker as BeanstalkWorker;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * BeanstalkWorkerFactory
 */
class BeanstalkWorkerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $workerOptions      = new WorkerOptions($serviceLocator->get('Config')['worker']);
        $queuePluginManager = $serviceLocator->get('SlmQueue\Queue\QueuePluginManager');

        return new BeanstalkWorker($queuePluginManager, $workerOptions);
    }
}
