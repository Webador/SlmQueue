<?php

namespace SlmQueue\Job;

use stdClass;
use Traversable;
use SlmQueue\Exception\InvalidArgumentException;
use Zend\Stdlib\ArrayUtils;

abstract class AbstractJob implements JobInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param null $options
     */
    public function __construct($options = null)
    {
        if ($options !== null) {
            $this->setOptions($options);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setOptions($options)
    {
        if($options !== null) {
            if ($options instanceof Traversable) {
                $options = ArrayUtils::iteratorToArray($options);
            } elseif ($options instanceof stdClass) {
                $options = get_object_vars($options);
            } elseif (!is_array($options)) {
                throw new InvalidArgumentException(
                    'The options parameter must be an array or a Traversable'
                );
            }
        }

        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    abstract public function __invoke();
}
