<?php

namespace SlmQueue\Queue;

use SlmQueue\ServiceManager\AbstractPluginManager;

/**
 * QueuePluginManager
 */
class QueuePluginManager extends AbstractPluginManager
{
    /**
     * {@inheritDoc}
     */
    public function validate($instance)
    {
        if ($instance instanceof QueueInterface) {
            return; // we're okay!
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement SlmQueue\Queue\QueueInterface',
            (is_object($instance) ? get_class($instance) : gettype($instance))
        ));
    }
}
