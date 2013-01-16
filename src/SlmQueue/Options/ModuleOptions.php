<?php

namespace SlmQueue\Options;

use Zend\Stdlib\AbstractOptions;

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
     * @param  int $maxRuns
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
     * @param  int $maxMemory
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
}