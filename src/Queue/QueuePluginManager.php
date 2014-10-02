<?php

namespace SlmQueue\Queue;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * QueuePluginManager
 */
class QueuePluginManager extends AbstractPluginManager
{
    /**
     * {@inheritDoc}
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof QueueInterface) {
            return; // we're okay!
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement SlmQueue\Queue\QueueInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
