<?php

namespace SlmQueue\AJob;

use Zend\ServiceManager\AbstractPluginManager;

class JobPluginManager extends AbstractPluginManager
{
    /**
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * @param  mixed $plugin
     * @return bool|void
     */
    public function validatePlugin($plugin)
    {
        return $plugin instanceof JobInterface;
    }
}