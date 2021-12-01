<?php

return [
    'modules' => [
        'Laminas\Router',
        'SlmQueue',
        'TestModule',
    ],
    'module_listener_options' => [
        'config_glob_paths' => [
            'config/autoload/{,*.}{global,local}.php',
        ],
        'module_paths' => [
            './vendor',
            './module',
        ],
    ],
];
