<?php

return [
    \SlmQueueTest\Asset\FileQueue::class => [
        'filename' => 'temp/queue',
    ],

    'slm_queue' => [
        'queues' => [
            'default' => [],
        ],
        'queue_manager' => [
            'factories' => [
                'default' => \SlmQueueTest\Asset\FileQueueFactory::class,
            ],
        ],
        'worker_strategies' => [
            'default' => [
                \SlmQueue\Strategy\MaxRunsStrategy::class => ['max_runs' => 1],
                \SlmQueue\Strategy\WorkerLifetimeStrategy::class => ['lifetime' => 3],
            ],
        ],
    ],
];
