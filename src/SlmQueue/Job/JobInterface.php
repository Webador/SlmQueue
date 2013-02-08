<?php

namespace SlmQueue\Job;

use Zend\Stdlib\MessageInterface;

/**
 * A job is a task inserted into a queue, and it contains metadata and content.
 *
 * In order to handle dependencies, each job should be pulled from the JobPluginManager (which is injected
 * into every queue).
 */
interface JobInterface extends MessageInterface
{
    /**
     * Set the identifier of the job (it proxies to its metadata)
     *
     * @param  mixed $id
     * @return JobInterface
     */
    public function setId($id);

    /**
     * Get the identifier of the job (it proxies to its metadata)
     *
     * @return mixed
     */
    public function getId();

    /**
     * Does the job has this metadata?
     *
     * @param  string $key
     * @return bool
     */
    public function hasMetadata($key);

    /**
     * Encode the data to Json in order to save it (we cannot use JsonSerializable because we need to support
     * PHP 5.3)
     *
     * @return string
     */
    public function jsonSerialize();

    /**
     * Execute the job
     *
     * @return void
     */
    public function execute();
}
