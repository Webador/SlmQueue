<?php

namespace SlmQueue\Factory;

use SlmQueue\Options\WorkerOptions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WorkerOptionsFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new WorkerOptions($serviceLocator->get('Config')['slm_queue']['worker']);
    }
}
