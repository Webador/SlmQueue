<?php

namespace SlmQueue\Worker;

use Laminas\ServiceManager\AbstractPluginManager;
use SlmQueue\Factory\WorkerAbstractFactory;
use SlmQueue\Worker\WorkerInterface;

/**
 * @method WorkerInterface get(string $name, ?array $options = null)
 */
class WorkerPluginManager extends AbstractPluginManager
{
    protected $instanceOf = WorkerInterface::class;

    public function __construct($configInstanceOrParentLocator = null, array $config = [])
    {
        parent::__construct($configInstanceOrParentLocator, $config);

        $this->addAbstractFactory(new WorkerAbstractFactory());
    }
}
