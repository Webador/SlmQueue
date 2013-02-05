<?php

namespace SlmQueue\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * WorkerOptions
 */
class WorkerOptions extends AbstractOptions
{
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
     * @return void
     */
    public function setMaxRuns($maxRuns)
    {
        $this->maxRuns = (int) $maxRuns;
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
     * @return void
     */
    public function setMaxMemory($maxMemory)
    {
        $this->maxMemory = (int) $maxMemory;
    }

    /**
     * @return int
     */
    public function getMaxMemory()
    {
        return $this->maxMemory;
    }
}