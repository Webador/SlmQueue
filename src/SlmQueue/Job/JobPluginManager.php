<?php

namespace SlmQueue\Job;

use SlmQueue\Queue\QueueAwareInterface;
use SlmQueue\Queue\QueueInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;

/**
 * JobPluginManager
 */
class JobPluginManager extends AbstractPluginManager implements QueueAwareInterface
{
    /**
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * @var QueueInterface
     */
    protected $queue;

    /**
     * {@inheritDoc}
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
        $self = $this;
        $this->addInitializer(function ($instance) use ($self) {
            if ($instance instanceof QueueAwareInterface && null !== $self->getQueue()) {
                $instance->setQueue($self->getQueue());
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * {@inheritDoc}
     */
    public function setQueue(QueueInterface $queue)
    {
        $this->queue = $queue;
    }

    /**
     * @param  mixed $plugin
     * @throws Exception\RuntimeException
     * @return void
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof JobInterface) {
            return; // we're okay
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement SlmQueue\Job\JobInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
