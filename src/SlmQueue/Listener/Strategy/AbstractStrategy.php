<?php

namespace SlmQueue\Listener\Strategy;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\Filter\Word\UnderscoreToCamelCase;

abstract class AbstractStrategy extends AbstractListenerAggregate
{

    /**
     * The final state of the strategy when exiting
     *
     * @var string | null
     */
    protected $exitState;

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
    public function getExitState()
    {
        return is_string($this->exitState) ? $this->exitState : false;
    }

}
