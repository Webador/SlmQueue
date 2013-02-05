<?php

namespace SlmQueue\OldJob;

interface JobInterface
{
    /**
     * Execute the job
     *
     * @return void
     */
    public function __invoke();

    /**
     * Set the options for the job
     *
     * @param  array|\Traversable $options
     * @return JobInterface
     */
    public function setOptions ($options);

    /**
     * Get the options for the job
     *
     * @return array
     */
    public function getOptions();

    /**
     * Set the identifier of the job
     *
     * @param  $id
     * @return JobInterface
     */
    public function setId($id);

    /**
     * Get the identifier of the job
     *
     * @return mixed
     */
    public function getId();
}
