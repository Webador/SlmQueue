<?php

namespace SlmQueue\Strategy;

use Laminas\EventManager\AbstractListenerAggregate;
use SlmQueue\Exception;
use SlmQueue\Worker\Event\ProcessStateEvent;
use SlmQueue\Worker\Result\ProcessStateResult;

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
     * @param array $options
     */
    public function __construct(array $options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Set options from array
     *
     * @param array $options
     * @return void
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $setter = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (! method_exists($this, $setter)) {
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
     * @param ProcessStateEvent $event
     * @return bool|string
     */
    public function onReportQueueState(ProcessStateEvent $event)
    {
        return is_string($this->state) ? ProcessStateResult::withState($this->state) : false;
    }
}
