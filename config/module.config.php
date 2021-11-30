<?php

use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use SlmQueue\Command\StartWorkerCommand;
use SlmQueue\Factory\JobPluginManagerFactory;
use SlmQueue\Factory\QueueControllerPluginFactory;
use SlmQueue\Factory\QueuePluginManagerFactory;
use SlmQueue\Factory\StrategyPluginManagerFactory;
use SlmQueue\Factory\WorkerPluginManagerFactory;
use SlmQueue\Job\JobPluginManager;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueue\Strategy\AttachQueueListenersStrategy;
use SlmQueue\Strategy\Factory\AttachQueueListenersStrategyFactory;
use SlmQueue\Strategy\Factory\LogJobStrategyFactory;
use SlmQueue\Strategy\FileWatchStrategy;
use SlmQueue\Strategy\InterruptStrategy;
use SlmQueue\Strategy\LogJobStrategy;
use SlmQueue\Strategy\MaxMemoryStrategy;
use SlmQueue\Strategy\MaxPollingFrequencyStrategy;
use SlmQueue\Strategy\MaxRunsStrategy;
use SlmQueue\Strategy\ProcessQueueStrategy;
use SlmQueue\Strategy\StrategyPluginManager;
use SlmQueue\Strategy\WorkerLifetimeStrategy;
use SlmQueue\Worker\WorkerPluginManager;

return [
    'service_manager' => [
        'factories' => [
            JobPluginManager::class => JobPluginManagerFactory::class,
            StrategyPluginManager::class => StrategyPluginManagerFactory::class,
            QueuePluginManager::class => QueuePluginManagerFactory::class,
            WorkerPluginManager::class => WorkerPluginManagerFactory::class,

            StartWorkerCommand::class => ReflectionBasedAbstractFactory::class,
        ],
    ],

    'laminas-cli' => [
        'commands' => [
            'slm-queue:start' => StartWorkerCommand::class,
        ],
    ],

    'controller_plugins' => [
        'factories' => [
            'queue' => QueueControllerPluginFactory::class,
        ],
    ],

    'slm_queue' => [
        /**
         * Worker Strategies
         */
        'worker_strategies' => [
            'default' => [ // per worker
                /*
                AttachQueueListenersStrategy::class, // attaches strategies per queue
                MaxRunsStrategy::class => ['max_runs' => 100000],
                MaxMemoryStrategy::class => ['max_memory' => 100 * 1024 * 1024],
                InterruptStrategy::class,
                 */
            ],
            'queues' => [ // per queue
                'default' => [
                    ProcessQueueStrategy::class,
                ],
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
         * Worker manager configuration
         */
        'worker_manager' => [],

        /**
         * Strategy manager configuration
         */
        'strategy_manager' => [
            'invokables' => [
                ProcessQueueStrategy::class => ProcessQueueStrategy::class,
                InterruptStrategy::class => InterruptStrategy::class,
                MaxRunsStrategy::class => MaxRunsStrategy::class,
                WorkerLifetimeStrategy::class => WorkerLifetimeStrategy::class,
                MaxMemoryStrategy::class => MaxMemoryStrategy::class,
                FileWatchStrategy::class => FileWatchStrategy::class,
                MaxPollingFrequencyStrategy::class => MaxPollingFrequencyStrategy::class,
            ],
            'factories' => [
                AttachQueueListenersStrategy::class => AttachQueueListenersStrategyFactory::class,
                LogJobStrategy::class => LogJobStrategyFactory::class,
            ],
        ],
    ],
];
