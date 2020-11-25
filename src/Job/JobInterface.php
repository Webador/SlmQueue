<?php

namespace SlmQueue\Job;

use Laminas\Stdlib\MessageInterface;

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
     * @param mixed $id
     */
    public function setId($id): JobInterface;

    /**
     * Get the identifier of the job (it proxies to its metadata)
     *
     * @return mixed
     */
    public function getId();

    /**
     * Execute the job.
     *
     * TODO Deprecate the usage of int as return value, and introduce exceptions as part of the API to signal a
     *   non-success result.
     *
     * @return void|?int Omitting return value, or returning `null` means the job was successful. Otherwise the int
     *   returned will represent.
     */
    public function execute();
}
