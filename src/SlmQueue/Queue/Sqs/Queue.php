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
        $queue          = $this->sqsClient->createQueue(array('QueueName' => $name));
        $this->queueUrl = $queue['QueueUrl'];
    }

    /**
     * {@inheritDoc}
     */
    public function push(JobInterface $job)
    {
        $parameters = array(
            'QueueUrl'     => $this->queueUrl,
            'MessageBody'  => json_encode($job),
            'DelaySeconds' => ($job->hasMetadata('delay') ? $job->getMetadata('delay') : null)
        );

        $result = $this->sqsClient->sendMessage(array_filter($parameters));

        $job->setId($result['MessageId']);
    }

    /**
     * {@inheritDoc}
     */
    public function batchPush(array $jobs)
    {
        $messages = array();
        foreach ($jobs as $job) {
            // Set a unique identifier for the job in the batch, so that we can set the identifier accordingly
            $job->setMetadata('batchId', uniqid());

            $message = array(
                'Id'           => $job->getMetadata('batchId'),
                'MessageBody'  => json_encode($job),
                'DelaySeconds' => ($job->hasMetadata('delay') ? $job->getMetadata('delay') : null)
            );

            $messages[] = array_filter($message);
        }

        $parameters = array(
            'QueueUrl' => $this->queueUrl,
            'Entries'  => array(
                $messages
            )
        );

        $result = $this->sqsClient->sendMessageBatch($parameters)['Successful']['items'];
        foreach ($jobs as $job) {
            $batchId    = $job->getMetadata('batchId');
            //$resultItem = array_search()
        }
    }

    /**
     * {@inheritDoc}
     */
    public function pop()
    {
        $result = $this->sqsClient->receiveMessage(array(
            'QueueUrl' => $this->queueUrl
        ));

        $messages = $result['Messages']['items'];

        $jobs = array();
        foreach ($messages as $message) {
            $jobs[] = $this->convertJob($message);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete(JobInterface $job)
    {
        $parameters = array(
            'QueueUrl'      => $this->queueUrl,
            'ReceiptHandle' => $job->getMetadata('receiptHandle')
        );

        $result = $this->sqsClient->sendMessage($parameters);

        $job->setId($result['MessageId']);
    }

    /**
     * {@inheritDoc}
     */
    public function batchDelete(array $jobs)
    {
        $messages = array();
        foreach ($jobs as $job) {
            // Set a unique identifier for the job in the batch, so that we can set the identifier accordingly
            $job->setMetadata('batchId', uniqid());

            $messages[] = array(
                'Id'            => $job->getMetadata('batchId'),
                'ReceiptHandle' => $job->getMetadata('receiptHandle')
            );
        }

        $parameters = array(
            'QueueUrl' => $this->queueUrl,
            'Entries'  => array(
                $messages
            )
        );

        $this->sqsClient->deleteMessageBatch($parameters);
    }

    /**
     * Convert an Amazon SQS result to our JobInterface
     *
     * @param  array $message
     * @return JobInterface
     */
    private function convertJob(array $message)
    {
        $data = json_decode($message['Body']);

        /** @var $job JobInterface */
        $job = $this->jobPluginManager->get($data['class']);
        $job->setId($message['MessageId'])
            ->setMetadata('receiptHandle', $message['ReceiptHandle'])
            ->setContent($data['content']);

        return $job;
    }
}
