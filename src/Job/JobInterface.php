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
     * Execute the job
     */
    public function execute(): ?int;
}
