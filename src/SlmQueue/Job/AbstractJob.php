<?php

namespace SlmQueue\Job;

/**
 * This class is supposed to be extended. To create a job, just implements the missing "execute" method. If a queueing
 * system needs more information, you can extend this class (but for both Beanstalk and SQS this is enough)
 */
abstract class AbstractJob implements JobInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var mixed
     */
    protected $content;


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
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * {@inheritDoc}
     */
    function jsonSerialize()
    {
        return array(
            'class'   => get_called_class(),
            'content' => $this->getContent()
        );
    }

    /**
     * {@inheritDoc}
     */
    abstract public function execute();
}
