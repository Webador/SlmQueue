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
     * @var string|array|null
     */
    protected $content = null;

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {
        $this->setMetadata('__id__', $id);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->getMetadata('__id__');
    }

    /**
     * {@inheritDoc}
     */
    public function chainJob(AbstractJob $job)
    {
        $chain   = $this->getMetadata('__jobchain__', array());
        $chain[] = $job;
        $this->setMetadata('__jobchain__', $chain);
    }
}
