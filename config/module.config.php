<?php

return array(
    'di' => array(
         'definition' => array(
            'class' => array(
                'SlmQueue\Service\PheanstalkFactory' => array(
                    'instantiator' => array('SlmQueue\Service\PheanstalkFactory', 'create'),
                    'methods' => array(
                        'create' => array(
                            'options' => array('type' => false, 'required' => true),
                        ),
                    ),
                ),
            ),
        ),
        'instance' => array(
            'alias' => array(
                'Pheanstalk' => 'SlmQueue\Service\PheanstalkFactory'
            ),
            
            'SlmQueue\Service\PheanstalkFactory' => array(
                'parameters' => array(
                    'options' => array(
                        'host' => '0.0.0.0',
                    )
                ),
            ),
        ),
    ),
);