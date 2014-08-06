<?php

namespace SlmQueue\Listener\Strategy;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\Filter\Word\UnderscoreToCamelCase;

abstract class AbstractStrategy extends AbstractListenerAggregate
{

    /**
     * The state of the strategy
     *
     * @var string | null
     */
    protected $state;

    /**
     * Set options from array
     */
    public function setOptions(array $options)
    {
        $filter = new UnderscoreToCamelCase();

        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($filter->filter($key));
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * @return false|string
     */
    public function getState()
    {
        return is_string($this->state) ? $this->state : false;
    }
}
