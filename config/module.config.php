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
         * Worker options
         */
        'strategies' => array(
            'common' => array( // per worker
                array('name' => 'SlmQueue\Strategy\MaxRunsStrategy', 'options' => array('max_runs' => 100000)),
                array('name' => 'SlmQueue\Strategy\MaxMemoryStrategy', 'options' => array('max_memory' => 100 * 1024 * 1024)),
                array('name' => 'SlmQueue\Strategy\FileWatchStrategy'),
                array('name' => 'SlmQueue\Strategy\InterruptStrategy', 'priority' => - PHP_INT_MAX),
            ),
            'queues' => array( // per queue
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
                'SlmQueue\Strategy\InterruptStrategy'       => 'SlmQueue\Listener\Strategy\InterruptStrategy',
                'SlmQueue\Strategy\MaxRunsStrategy'         => 'SlmQueue\Listener\Strategy\MaxRunsStrategy',
                'SlmQueue\Strategy\MaxMemoryStrategy'       => 'SlmQueue\Listener\Strategy\MaxMemoryStrategy',
                'SlmQueue\Strategy\FileWatchStrategy'       => 'SlmQueue\Listener\Strategy\FileWatchStrategy',
            ),
        ),
    )
);
