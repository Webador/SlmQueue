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
     * Set how many jobs can be processed before the worker stops
     *
     * @param  int $maxRuns
     * @return void
     */
    public function setMaxRuns($maxRuns)
    {
        $this->maxRuns = (int) $maxRuns;
    }

    /**
     * Get how many jobs can be processed before the worker stops
     *
     * @return int
     */
    public function getMaxRuns()
    {
        return $this->maxRuns;
    }

    /**
     * Set the max memory the worker can use (in bytes)
     *
     * @param  int $maxMemory
     * @return void
     */
    public function setMaxMemory($maxMemory)
    {
        $this->maxMemory = (int) $maxMemory;
    }

    /**
     * Get the max memory the worker can use (in bytes)
     *
     * @return int
     */
    public function getMaxMemory()
    {
        return $this->maxMemory;
    }
}
