<?php
namespace SlmQueue\Strategy\Factory;

use SlmQueue\Strategy\LogJobStrategy;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * LogJobStrategyFactory
 */
class LogJobStrategyFactory implements FactoryInterface
{
    protected $options;

    public function __construct(array $options = null)
    {
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $strategy = new LogJobStrategy($container->get('console'), $this->options);

        return $strategy;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return LogJobStrategy
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), LogJobStrategy::class);
    }
}
