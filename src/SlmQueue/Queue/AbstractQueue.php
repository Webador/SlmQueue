<?php

namespace SlmQueue\Queue;

/**
 * AbstractQueue
 */
abstract class AbstractQueue implements QueueInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var object
     */
    protected $options;


    /**
     * @param string      $name
     * @param object|null $options
     */
    public function __construct($name, $options = null)
    {
        $this->name = $name;

        if ($this->options !== null) {
            $this->options = $options;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return $this->options;
    }
}
