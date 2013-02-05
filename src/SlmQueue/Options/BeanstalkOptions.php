<?php

namespace SlmQueue\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * BeanstalkOptions
 */
class BeanstalkOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @var string
     */
    protected $defaultTube;

    /**
     * @var BeanstalkTubeOptions[]
     */
    protected $tubes;


    /**
     * Set the host
     *
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = (string) $host;
    }

    /**
     * Get the host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set the port
     *
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = (int) $port;
    }

    /**
     * Get the port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set the connection timeout
     *
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = (int) $timeout;
    }

    /**
     * Get the connection timeout
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Set the default tube
     *
     * @param string $defaultTube
     */
    public function setDefaultTube($defaultTube)
    {
        $this->defaultTube = $defaultTube;
    }

    /**
     * Get the default tube
     *
     * @return string
     */
    public function getDefaultTube()
    {
        return $this->defaultTube;
    }

    /**
     * Set the options for the tubes
     *
     * @param array $tubes
     */
    public function setTubes(array $tubes)
    {
        foreach ($tubes as $name => $options) {
            $this->tubes[$name] = new BeanstalkTubeOptions($options);
        }
    }

    /**
     * Get the tube options
     *
     * @return BeanstalkTubeOptions[]
     */
    public function getTubes()
    {
        return $this->tubes;
    }
}
