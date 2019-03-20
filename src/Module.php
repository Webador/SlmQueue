<?php

namespace SlmQueue;

/**
 * SlmQueue
 */
class Module
{
    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
