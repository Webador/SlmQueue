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
     * Execute the job
     *
     * @return void
     */
    public function execute();

    /**
     * Chain a job.
     *
     * When the job has finished these jobs will be added to the queue in the same order they where added.
     *
     * @param AbstractJob $job
     */
    public function chainJob(AbstractJob $job);
}
