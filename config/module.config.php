<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'SlmQueue\Job\JobPluginManager'             => 'SlmQueue\Factory\JobPluginManagerFactory',
            'SlmQueue\Listener\ListenerPluginManager'   => 'SlmQueue\Factory\ListenerPluginManagerFactory',
            'SlmQueue\Options\WorkerOptions'            => 'SlmQueue\Factory\WorkerOptionsFactory',
            'SlmQueue\Queue\QueuePluginManager'         => 'SlmQueue\Factory\QueuePluginManagerFactory'
        ),
    ),

    'slm_queue' => array(
        /**
         * Worker options
         */
        'worker' => array(
            'max_runs'   => 1,
            'max_memory' => 2,
        ),

        /**
         * Queue configuration
         */
        'queues' => array(),

        /**
         * Register worker listeners
         */
        'strategies' => array(),

        /**
         * Job manager configuration
         */
        'job_manager' => array(),

        /**
         * Queue manager configuration
         */
        'queue_manager' => array(),

        /**
         * Listener manager configuration
         */
        'listener_manager' => array(
            'invokables' => array(
                'SlmQueue\Strategy\InterruptStrategy'   => 'SlmQueue\Listener\Strategy\InterruptStrategy', // required hardwired strategy
                'SlmQueue\Strategy\MaxRunsStrategy'     => 'SlmQueue\Listener\Strategy\MaxRunsStrategy',   // required hardwired strategy
                'SlmQueue\Strategy\MaxMemoryStrategy'   => 'SlmQueue\Listener\Strategy\MaxMemoryStrategy', // required hardwired strategy

                // some idea's for strategies
//                'SlmQueue\Strategy\FreeDiskSpaceStrategy', a minimum amount of disk space may be required before jobs are started
//                'SlmQueue\Strategy\MinMemoryStrategy', a minimum amount of memory may be required before jobs are started
//                'SlmQueue\Strategy\PeakMemoryStrategy', stop when memory consumption has peaked a threshold
//                'SlmQueueDoctrine\Strategy\SleepWhileIdleStrategy', doctrine should sleep when no job are available, the queue handles this currently, could be moved to the worker
//                'SlmQueue\Strategy\WatchFileStrategy', when a certain file has changed stop, to be restarted by supervisor, ideal when there are regular deployments
            ),
        ),
    )
);
