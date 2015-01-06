<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'SlmQueue\Job\JobPluginManager'             => 'SlmQueue\Factory\JobPluginManagerFactory',
            'SlmQueue\Listener\StrategyPluginManager'   => 'SlmQueue\Factory\StrategyPluginManagerFactory',
            'SlmQueue\Queue\QueuePluginManager'         => 'SlmQueue\Factory\QueuePluginManagerFactory'
        ),
    ),

    'controller_plugins' => array(
        'factories' => array(
            'queue' => 'SlmQueue\Factory\QueueControllerPluginFactory'
        ),
    ),

    'slm_queue' => array(
        /**
         * Worker Strategies
         */
        'worker_strategies' => array(
            'default' => array( // per worker
                'SlmQueue\Strategy\AttachQueueListenersStrategy', // attaches strategies per queue
                'SlmQueue\Strategy\MaxRunsStrategy' => array('max_runs' => 100000),
                'SlmQueue\Strategy\MaxMemoryStrategy' => array('max_memory' => 100 * 1024 * 1024),
                'SlmQueue\Strategy\InterruptStrategy',
                'SlmQueue\Strategy\MaxPollingFrequencyStrategy' => array('max_frequency' => 0)
            ),
            'queues' => array( // per queue
                'default' => array(
                    'SlmQueue\Strategy\ProcessQueueStrategy',
                )
            ),
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

        /**
         * Strategy manager configuration
         */
        'strategy_manager' => array(
            'invokables' => array(
                'SlmQueue\Strategy\ProcessQueueStrategy'    => 'SlmQueue\Strategy\ProcessQueueStrategy',
                'SlmQueue\Strategy\InterruptStrategy'       => 'SlmQueue\Strategy\InterruptStrategy',
                'SlmQueue\Strategy\MaxRunsStrategy'         => 'SlmQueue\Strategy\MaxRunsStrategy',
                'SlmQueue\Strategy\MaxMemoryStrategy'       => 'SlmQueue\Strategy\MaxMemoryStrategy',
                'SlmQueue\Strategy\FileWatchStrategy'       => 'SlmQueue\Strategy\FileWatchStrategy',
                'SlmQueue\Strategy\MaxPollingFrequencyStrategy' => 'SlmQueue\Strategy\MaxPollingFrequencyStrategy',
            ),
            'factories' => array(
                'SlmQueue\Strategy\AttachQueueListenersStrategy' => 'SlmQueue\Strategy\Factory\AttachQueueListenersStrategyFactory',
                'SlmQueue\Strategy\LogJobStrategy'               => 'SlmQueue\Strategy\Factory\LogJobStrategyFactory',
            )
        ),
    )
);
