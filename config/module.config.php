<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'SlmQueue\Job\JobPluginManager'     => 'SlmQueue\Factory\JobPluginManagerFactory',
            'SlmQueue\Options\WorkerOptions'    => 'SlmQueue\Factory\WorkerOptionsFactory',
            'SlmQueue\Queue\QueuePluginManager' => 'SlmQueue\Factory\QueuePluginManagerFactory'
        )
    ),

    'slm_queue' => array(
        /**
         * Parameters for the worker
         */
        'worker' => array(
            'max_runs'   => 100000,
            'max_memory' => 1048576
        ),

        /**
         * Jobs
         */
        'jobs' => array(),

        /**
         * Queues
         */
        'queues' => array()
    ),
);
