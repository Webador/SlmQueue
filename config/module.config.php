<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'SlmQueue\Job\JobPluginManager'     => 'SlmQueue\Factory\JobPluginManagerFactory',
            'SlmQueue\Options\WorkerOptions'    => 'SlmQueue\Factory\WorkerOptionsFactory',
            'SlmQueue\Queue\QueuePluginManager' => 'SlmQueue\Factory\QueuePluginManagerFactory'
        ),
    ),

    'controller_plugins' => array(
        'factories' => array(
            'queue' => 'SlmQueue\Factory\QueueControllerPluginFactory'
        ),
    ),

    'slm_queue' => array(
        /**
         * Worker options
         */
        'worker' => array(
            'max_runs'   => 100000,
            'max_memory' => 100 * 1024 * 1024
        ),

        /**
         * Queue configuration
         */
        'queues' => array(),

        /**
         * Job manager configuration
         */
        'job_manager' => array(),

        /**
         * Queue manager configuration
         */
        'queue_manager' => array(),
    )
);
