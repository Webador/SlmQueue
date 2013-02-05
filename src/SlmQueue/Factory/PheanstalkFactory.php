<?php

namespace SlmQueue\Factory;

use Pheanstalk_Pheanstalk as Pheanstalk;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * PheanstalkFactory
 */
class PheanstalkFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config')['beanstalk']['connection'];
        return new Pheanstalk($config['host'], $config['port'], $config['timeout']);
    }
}
