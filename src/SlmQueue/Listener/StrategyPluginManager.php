<?php

namespace SlmQueue\Listener;

use SlmQueue\Listener\Strategy\AbstractStrategy;
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
     * @throws Exception\RuntimeException
     * @return void
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof AbstractStrategy) {
            return; // we're okay
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must extend SlmQueue\Listener\Strategy\AbstractStrategy',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
