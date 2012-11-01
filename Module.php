<?php

namespace SlmQueue;

use Zend\Loader;
use Zend\ModuleManager\Feature;

class Module implements
    Feature\AutoloaderProviderInterface,
    Feature\ConfigProviderInterface,
    Feature\ServiceProviderInterface
{
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

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Pheanstalk' => 'SlmQueue\Service\PheanstalkFactory',

                'SlmQueue\Options\ModuleOptions'    => function($sm) {
                    $config = $sm->get('config');
                    return new Options\ModuleOptions($config['slm_queue']);
                },
                'SlmQueue\Service\PheanstalkBridge' => function($sm) {
                    $pheanstalk = $sm->get('Pheanstalk');
                    $service    = new Service\PheanstalkBridge($pheanstalk);
                    return $service;
                },
            ),
        );
    }

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
