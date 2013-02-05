<?php

namespace SlmQueue\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * BeanstalkOptions
 */
class BeanstalkOptions extends AbstractOptions
{
    /**
     * @var int
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var int
     */
    protected $connectionTimeout;

    /**
     * @var BeanstalkTubeOptions[]
     */
    protected $tubes;


    /**
     * Set the host
     *
     * @param int $host
     */
    public function setHost($host)
    {
        $this->host = (int) $host;
    }

    /**
     * Get the host
     *
     * @return int
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
     * @param int $connectionTimeout
     */
    public function setConnectionTimeout($connectionTimeout)
    {
        $this->connectionTimeout = (int) $connectionTimeout;
    }

    /**
     * Get the connection timeout
     *
     * @return int
     */
    public function getConnectionTimeout()
    {
        return $this->connectionTimeout;
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
