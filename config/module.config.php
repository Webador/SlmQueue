<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'SlmQueue\Job\JobPluginManager'     => 'SlmQueue\Factory\JobPluginManagerFactory',
            'SlmQueue\Options\WorkerOptions'    => 'SlmQueue\Factory\WorkerOptionsFactory',
            'SlmQueue\Queue\QueuePluginManager' => 'SlmQueue\Factory\QueuePluginManagerFactory'
        ),
    ),

    'slm_queue' => array(
        /**
         * Parameters for the worker
         */
        'worker' => array(
            'max_runs'   => 100000,
            'max_memory' => 100 * 1024 * 1024
        ),

        /**
         * Job manager configuration
         */
        'job_manager' => array(),

        /**
         * Queue manager configuration
         */
        'queue_manager' => array(),

        /**
         * Queue configuration options
         */
        'queues' => array(),
    ),
);
