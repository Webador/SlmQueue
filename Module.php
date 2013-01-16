<?php

namespace SlmQueue;

use Zend\Loader;
use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature;

class Module implements
    Feature\AutoloaderProviderInterface,
    Feature\ConfigProviderInterface,
    Feature\ConsoleBannerProviderInterface,
    Feature\ConsoleUsageProviderInterface,
    Feature\ControllerPluginProviderInterface,
    Feature\ControllerProviderInterface,
    Feature\ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            Loader\AutoloaderFactory::STANDARD_AUTOLOADER => array(
                Loader\StandardAutoloader::LOAD_NS => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }


    /**
     * {@inheritDoc}
     */
    public function getConsoleBanner(AdapterInterface $console)
    {
        return '----------------------------------------------------------------------' . PHP_EOL .
               'SlmQueue | Pheanstalk Zend Framework 2 module' . PHP_EOL .
               '----------------------------------------------------------------------' . PHP_EOL;
    }

    /**
     * {@inheritDoc}
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return array(
            'queue --start' => 'Start to execute the jobs in the queue'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Pheanstalk' => 'SlmQueue\Service\PheanstalkFactory',

                'SlmQueue\Options\ModuleOptions' => function($sm) {
                    $config = $sm->get('Config');
                    return new Options\ModuleOptions($config['slm_queue']);
                },

                'SlmQueue\Service\PheanstalkBridge' => function($sm) {
                    $pheanstalk = $sm->get('Pheanstalk');
                    $service    = new Service\PheanstalkBridge($pheanstalk);
                    return $service;
                },

                'SlmQueue\Job\JobPluginManager' => function($sm) {
                    /** @var $options Options\ModuleOptions */
                    $options = $sm->get('SlmQueue\Options\ModuleOptions');
                    return new Job\JobPluginManager($options->getJobManagerOptions());
                },
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getControllerConfig()
    {
        return array(
            'factories' => array(
                'SlmQueue\Controller\WorkerController' => function($sm) {
                    $beanstalk = $sm->getServiceLocator()->get('SlmQueue\Service\PheanstalkBridge');
                    $options   = $sm->getServiceLocator()->get('SlmQueue\Options\ModuleOptions');

                    $controller = new Controller\WorkerController($beanstalk, $options);
                    return $controller;
                },
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'queue' => function($sm) {
                    $beanstalk = $sm->getServiceLocator()->get('SlmQueue\Service\PheanstalkBridge');
                    $plugin    = new Controller\Plugin\Queue($beanstalk);
                    return $plugin;
                }
            ),
        );
    }
}
