<?php

namespace SlmQueue\Job;

use JsonSerializable;

/**
 * A job is a task inserted into a queue (it contains metadata and content)
 *
 * In order to handle dependencies, each job should be pulled from the JobPluginManager (which is injected
 * into every queue).
 */
interface JobInterface extends JsonSerializable
{
    /**
     * Set the identifier of the job
     *
     * @param  mixed $id
     * @return JobInterface
     */
    public function setId($id);

    /**
     * Get the identifier of the job
     *
     * @return mixed
     */
    public function getId();

    /**
     * Set the content of the job
     *
     * @param  mixed $content
     * @return JobInterface
     */
    public function setContent($content);

    /**
     * Get the content of the job
     *
     * @return mixed
     */
    public function getContent();

    /**
     * Execute the job
     *
     * @return void
     */
    public function execute();
}
