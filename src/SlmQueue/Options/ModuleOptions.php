<?php

namespace SlmQueue\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * ModuleOptions
 */
class ModuleOptions extends AbstractOptions
{
    /**
     * @var bool
     */
    protected $__strictMode__ = false;

    /**
     * @var int
     */
    protected $maxRuns;

    /**
     * @var int
     */
    protected $maxMemory;


    /**
     * @param  int $maxRuns
     * @return ModuleOptions
     */
    public function setMaxRuns($maxRuns)
    {
        $this->maxRuns = (int) $maxRuns;
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
        $this->maxMemory = (int) $maxMemory;
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