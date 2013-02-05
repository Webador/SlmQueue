<?php

namespace SlmQueue\Queue\Sqs;

use Aws\Sqs\SqsClient;
use SlmQueue\Job\JobPluginManager;
use SlmQueue\Queue\AbstractQueue;
use SlmQueue\Job\JobInterface;

/**
 * Provides a basic queue implementation for Amazon SQS
 */
class Queue extends AbstractQueue
{
    /**
     * @var SqsClient
     */
    protected $sqsClient;

    /**
     * @var string
     */
    protected $queueUrl;


    /**
     * Constructor
     *
     * @param SqsClient        $sqsClient
     * @param string           $name
     * @param JobPluginManager $jobPluginManager
     */
    public function __construct(SqsClient $sqsClient, JobPluginManager $jobPluginManager, $name)
    {
        $this->sqsClient = $sqsClient;
        parent::__construct($name, $jobPluginManager);

        // Retrieve the queue from Amazon SQS and store the URL
        //$queue = $this->sqsClient->createQueue(array())
    }

    /**
     * {@inheritDoc}
     */
    public function push(JobInterface $job, array $options = array())
    {
        $this->sqsClient->sendMessage(array(
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function batchPush(array $jobs, array $options = array())
    {
        // TODO: Implement batchPush() method.
    }

    /**
     * {@inheritDoc}
     */
    public function pop()
    {
        // TODO: Implement pop() method.
    }

    /**
     * {@inheritDoc}
     */
    public function delete(JobInterface $job)
    {
        // TODO: Implement delete() method.
    }

    /**
     * {@inheritDoc}
     */
    public function batchDelete(array $jobs)
    {
        // TODO: Implement batchDelete() method.
    }
}
