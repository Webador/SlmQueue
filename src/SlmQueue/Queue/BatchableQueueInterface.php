<?php

namespace SlmQueue\Queue;

use SlmQueue\Job\JobInterface;

/**
 * Contract for a batchable queue
 */
interface BatchableQueueInterface extends QueueInterface
{
    /**
     * Pop several jobs at once
     *
     * @param  array $options
     * @return JobInterface[]
     */
    public function batchPop(array $options = array());

    /**
     * Push several jobs at once
     *
     * @param  JobInterface[] $jobs
     * @param  array          $options
     * @return void
     */
    public function batchPush(array $jobs, array $options = array());

    /**
     * Delete several jobs at once
     *
     * @param  JobInterface[] $jobs
     * @return void
     */
    public function batchDelete(array $jobs);
}
