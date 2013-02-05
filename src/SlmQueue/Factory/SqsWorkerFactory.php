<?php

namespace SlmQueue\Factory;

use SlmQueue\Options\WorkerOptions;
use SlmQueue\Worker\Sqs\Worker as SqsWorker;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * SqsWorkerFactory
 */
class SqsWorkerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $workerOptions      = new WorkerOptions($serviceLocator->get('Config')['worker']);
        $queuePluginManager = $serviceLocator->get('SlmQueue\Queue\QueuePluginManager');

        return new SqsWorker($queuePluginManager, $workerOptions);
    }
}
