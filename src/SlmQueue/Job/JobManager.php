<?php

namespace SlmQueue\Job;

use Zend\ServiceManager\AbstractPluginManager;

class JobManager extends AbstractPluginManager
{
    protected $shareByDefault = false;

    public function validatePlugin($plugin)
    {
        return $plugin instanceof JobInterface;
    }
}