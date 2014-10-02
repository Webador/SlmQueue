<?php

namespace SlmQueue\Strategy;

use SlmQueue\Exception\RuntimeException;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * StrategyPluginManager
 */
class StrategyPluginManager extends AbstractPluginManager
{
    /**
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * @param  mixed $plugin
     * @throws RuntimeException
     * @return void
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof AbstractStrategy) {
            return; // we're okay
        }

        throw new RuntimeException(sprintf(
            'Plugin of type %s is invalid; must extend SlmQueue\Strategy\AbstractStrategy',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
