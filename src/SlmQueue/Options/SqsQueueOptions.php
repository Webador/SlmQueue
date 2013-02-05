<?php

namespace SlmQueue\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * SqsQueueOptions
 *
 * NOTE : Amazon SQS talks about "messages", but this is equivalent to what we call "job" in this module
 */
class SqsQueueOptions extends AbstractOptions
{
    /**
     * Visibility timeout for the queue (in seconds) - must be between 0 and 12 -
     *
     * @var int
     */
    protected $visibilityTimeout;

    /**
     * Maximum size of messages (in bytes) before Amazon SQS rejects it - must be between 1 and 64 KB -
     *
     * @var int
     */
    protected $maximumMessageSize;

    /**
     * Number of seconds Amazon SQS retains a job - must be between 1 minute and 14 days -
     *
     * @var int
     */
    protected $messageRetentionPeriod;

    /**
     * Delay of the queue in seconds - must be between 0 seconds and 15 minutes -
     *
     * @var int
     */
    protected $delaySeconds;

    /**
     * Number of seconds for which a ReceiveMessage call will wait for a message to arrive - must be between
     * 0 and 20 seconds -
     *
     * @var int
     */
    protected $receiveMessageWaitTimeSeconds;


    /**
     * Set the visibility timeout for the queue (in seconds)
     *
     * @param int $visibilityTimeout
     */
    public function setVisibilityTimeout($visibilityTimeout)
    {
        $this->visibilityTimeout = (int) $visibilityTimeout;
    }

    /**
     * Get the visibility timeout for the queue (in seconds)
     *
     * @return int
     */
    public function getVisibilityTimeout()
    {
        return $this->visibilityTimeout;
    }

    /**
     * Set the maximum size of a message (in bytes) before Amazon SQS rejects it
     *
     * @param int $maximumMessageSize
     */
    public function setMaximumMessageSize($maximumMessageSize)
    {
        $this->maximumMessageSize = (int) $maximumMessageSize;
    }

    /**
     * Get the maximum size of a message (in bytes) before Amazon SQS rejects it
     *
     * @return int
     */
    public function getMaximumMessageSize()
    {
        return $this->maximumMessageSize;
    }

    /**
     * Set the default delay on the queue (in seconds)
     *
     * @param int $delay
     */
    public function setDelayInSeconds($delay)
    {
        $this->delaySeconds = (int) $delay;
    }

    /**
     * Get the default delay on the queue (in seconds)
     *
     * @return int
     */
    public function getDelayInSeconds()
    {
        return $this->delaySeconds;
    }

    /**
     * Set the number of seconds for which a ReceiveMessage call will wait for a message to arrive
     *
     * @param int $receiveMessageWaitTimeSeconds
     */
    public function setReceiveMessageWaitTimeSeconds($receiveMessageWaitTimeSeconds)
    {
        $this->receiveMessageWaitTimeSeconds = (int) $receiveMessageWaitTimeSeconds;
    }

    /**
     * Get the number of seconds for which a ReceiveMessage call will wait for a message to arrive
     *
     * @return int
     */
    public function getReceiveMessageWaitTimeSeconds()
    {
        return $this->receiveMessageWaitTimeSeconds;
    }
}
