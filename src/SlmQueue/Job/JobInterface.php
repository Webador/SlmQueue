<?php

namespace SlmQueue\Job;

use JsonSerializable;

/**
 * A job is a task inserted into a queue.
 *
 * In order to handle dependencies, each job should be pulled from the JobPluginManager (which is injected
 * into every queue).
 */
interface JobInterface extends JsonSerializable
{
    /**
     * Execute the job
     *
     * @return void
     */
    public function execute();
}
