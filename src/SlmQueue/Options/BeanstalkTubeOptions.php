<?php

namespace SlmQueue\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * BeanstalkTubeOptions
 */
class BeanstalkTubeOptions extends AbstractOptions
{
    /**
     * If "use" is set to true, jobs can be pushed into the tube
     *
     * @var bool
     */
    protected $use;

    /**
     * If "watch" is set to true, jobs can be popped from the tube
     *
     * @var bool
     */
    protected $watch;

    /**
     * If "ignore" is true to true, jobs cannot be popped from the tube
     *
     * @var bool
     */
    protected $ignore;


    /**
     * @param bool $use
     */
    public function setUse($use)
    {
        $this->use = (bool) $use;
    }

    /**
     * @return bool
     */
    public function getUse()
    {
        return $this->use;
    }

    /**
     * @param bool $watch
     */
    public function setWatch($watch)
    {
        $this->watch = (bool) $watch;
    }

    /**
     * @return bool
     */
    public function getWatch()
    {
        return $this->watch;
    }

    /**
     * @param bool $ignore
     */
    public function setIgnore($ignore)
    {
        $this->ignore = (bool) $ignore;
    }

    /**
     * @return bool
     */
    public function getIgnore()
    {
        return $this->ignore;
    }
}
