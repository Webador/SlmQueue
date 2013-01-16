<?php

namespace SlmQueue\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\ServiceManager\Config as JobManagerOptions;

class ModuleOptions extends AbstractOptions
{
    /**
     * @var bool
     */
    protected $__strictMode__ = false;

    /**
     * @var int
     */
    protected $maxRuns = 100;

    /**
     * @var int
     */
    protected $maxMemory = 1024;

    /**
     * @var JobManagerOptions
     */
    protected $jobManagerOptions;

    /**
     * @param $maxRuns
     * @return ModuleOptions
     */
    public function setMaxRuns($maxRuns)
    {
        $this->maxRuns = $maxRuns;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxRuns()
    {
        return $this->maxRuns;
    }

    /**
     * @param $maxMemory
     * @return ModuleOptions
     */
    public function setMaxMemory($maxMemory)
    {
        $this->maxMemory = $maxMemory;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxMemory()
    {
        return $this->maxMemory;
    }

    /**
     * @return JobManagerOptions
     */
    public function getJobManagerOptions()
    {
        return $this->jobManagerOptions;
    }

    /**
     * @param array|JobManagerOptions $jobManagerOptions
     * @return ModuleOptions
     */
    public function setJobManagerOptions($jobManagerOptions)
    {
        if(!$jobManagerOptions instanceof JobManagerOptions)
        {
            $jobManagerOptions = new JobManagerOptions($jobManagerOptions);
        }
        $this->jobManagerOptions = $jobManagerOptions;
        return $this;
    }
}