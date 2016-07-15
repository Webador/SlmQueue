<?php

namespace SlmQueue;

use Zend\Loader;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * SlmQueue
 */
class Module implements ConfigProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
