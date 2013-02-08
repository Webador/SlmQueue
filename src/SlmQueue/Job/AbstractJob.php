<?php

namespace SlmQueue\Job;

use Zend\Stdlib\Message;

/**
 * This class is supposed to be extended. To create a job, just implements the missing "execute" method. If a queueing
 * system needs more information, you can extend this class (but for both Beanstalk and SQS this is enough)
 */
abstract class AbstractJob extends Message implements JobInterface
{
    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {
        $this->setMetadata('id', $id);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->getMetadata('id');
    }

    /**
     * {@inheritDoc}
     */
    public function hasMetadata($key)
    {
        return isset($this->metadata[$key]);
    }

    /**
     * The 'class' attribute that is saved allow to easily handle dependencies by pulling the job from
     * the JobPluginManager whenever it is popped from the queue
     *
     * @return string
     */
    function jsonSerialize()
    {
        $data = array(
            'class'   => get_called_class(),
            'content' => $this->getContent()
        );

        return json_encode($data);
    }

    /**
     * {@inheritDoc}
     */
    abstract public function execute();
}
