<?php

namespace SlmQueue\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * SqsOptions
 */
class SqsOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $configFile;

    /**
     * @var SqsQueueOptions[]
     */
    protected $queues;
}
