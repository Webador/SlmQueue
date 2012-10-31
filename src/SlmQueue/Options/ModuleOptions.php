<?php

namespace SlmQueue\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    protected $__strictMode__ = false;

    protected $maxRuns   = 100;
    protected $maxMemory = 1024;

    public function getMaxRuns()
    {
        return $this->maxRuns;
    }

    public function setMaxRuns($maxRuns)
    {
        $this->maxRuns = $maxRuns;
        return $this;
    }

    public function getMaxMemory()
    {
        return $this->maxMemory;
    }

    public function setMaxMemory($maxMemory)
    {
        $this->maxMemory = $maxMemory;
        return $this;
    }
}