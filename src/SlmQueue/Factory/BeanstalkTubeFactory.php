<?php

namespace SlmQueue\Factory;

use SlmQueue\Queue\Beanstalk\Tube;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * BeanstalkTubeFactory
 */
class BeanstalkTubeFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = '', $requestedName = '')
    {
        $pheanstalk       = $serviceLocator->get('SlmQueue\Service\PheanstalkService');
        $jobPluginManager = $serviceLocator->get('SlmQueue\Job\JobPluginManager');

        $tube = new Tube($pheanstalk, $jobPluginManager, $requestedName);
    }
}
