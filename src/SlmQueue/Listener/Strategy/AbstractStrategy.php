<?php

namespace SlmQueue\Listener\Strategy;

use SlmQueue\Exception;
use SlmQueue\Worker\WorkerEvent;
use Zend\EventManager\AbstractListenerAggregate;

abstract class AbstractStrategy extends AbstractListenerAggregate
{
    /**
     * The state of the strategy
     *
     * @var string | null
     */
    protected $state;

    /**
     * Constructor
     *
     * @param  array $options
     */
    public function __construct(array $options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Set options from array
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $setter = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (!method_exists($this, $setter)) {
                throw new Exception\BadMethodCallException(
                    'The option "' . $key . '" does not '
                    . 'have a matching ' . $setter . ' setter method '
                    . 'which must be defined'
                );
            }
            $this->{$setter}($value);
        }
    }

    /**
     * Event listener which returns the state of the queue
     *
     * @param WorkerEvent $event
     * @return bool|string
     */
    public function onReportQueueState(WorkerEvent $event)
    {
        return is_string($this->state) ? $this->state : false;
    }
}
