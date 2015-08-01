<?php

return [
    'service_manager' => [
        'factories' => [
            'SlmQueue\Job\JobPluginManager'             => 'SlmQueue\Factory\JobPluginManagerFactory',
            'SlmQueue\Listener\StrategyPluginManager'   => 'SlmQueue\Factory\StrategyPluginManagerFactory',
            'SlmQueue\Queue\QueuePluginManager'         => 'SlmQueue\Factory\QueuePluginManagerFactory'
        ],
    ],

    'controller_plugins' => [
        'factories' => [
            'queue' => 'SlmQueue\Factory\QueueControllerPluginFactory'
        ],
    ],

    'slm_queue' => [
        /**
         * Worker Strategies
         */
        'worker_strategies' => [
            'default' => [ // per worker
                'SlmQueue\Strategy\AttachQueueListenersStrategy', // attaches strategies per queue
                'SlmQueue\Strategy\MaxRunsStrategy'     => ['max_runs' => 100000],
                'SlmQueue\Strategy\MaxMemoryStrategy'   => ['max_memory' => 100 * 1024 * 1024],
                'SlmQueue\Strategy\InterruptStrategy',
            ],
            'queues' => [ // per queue
                'default' => [
                    'SlmQueue\Strategy\ProcessQueueStrategy',
                ]
            ],
        ],

        /**
         * Queue configuration
         */
        'queues' => [],

        /**
         * Job manager configuration
         */
        'job_manager' => [],

        /**
         * Queue manager configuration
         */
        'queue_manager' => [],

        /**
         * Strategy manager configuration
         */
        'strategy_manager' => [
            'invokables' => [
                'SlmQueue\Strategy\ProcessQueueStrategy'        => 'SlmQueue\Strategy\ProcessQueueStrategy',
                'SlmQueue\Strategy\InterruptStrategy'           => 'SlmQueue\Strategy\InterruptStrategy',
                'SlmQueue\Strategy\MaxRunsStrategy'             => 'SlmQueue\Strategy\MaxRunsStrategy',
                'SlmQueue\Strategy\MaxMemoryStrategy'           => 'SlmQueue\Strategy\MaxMemoryStrategy',
                'SlmQueue\Strategy\FileWatchStrategy'           => 'SlmQueue\Strategy\FileWatchStrategy',
                'SlmQueue\Strategy\MaxPollingFrequencyStrategy' => 'SlmQueue\Strategy\MaxPollingFrequencyStrategy',
            ],
            'factories' => [
                'SlmQueue\Strategy\AttachQueueListenersStrategy' => 'SlmQueue\Strategy\Factory\AttachQueueListenersStrategyFactory',
                'SlmQueue\Strategy\LogJobStrategy'               => 'SlmQueue\Strategy\Factory\LogJobStrategyFactory',
            ]
        ],
    ]
];
