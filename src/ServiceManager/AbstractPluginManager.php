<?php

namespace SlmQueue\ServiceManager;

use Laminas\ServiceManager\AbstractPluginManager as LaminasAbstractPluginManager;
use Laminas\Stdlib\DispatchableInterface as Dispatchable;
use Laminas\Mvc\Controller\Plugin\PluginInterface;

/**
 * AbstractPluginManager
 */
abstract class AbstractPluginManager extends LaminasAbstractPluginManager implements PluginInterface
{
    /**
     *
     * @var Dispatchable
     */
    protected $controller;

    /**
     * {@inheritDoc}
     */
    public function validatePlugin($plugin)
    {
        return $this->validate($plugin);
    }

    /**
     *
     * @return Dispatchable
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     *
     * @param Dispatchable $controller
     * @return AbstractPluginManager
     */
    public function setController(Dispatchable $controller)
    {
        $this->controller = $controller;
        return $this;
    }
}
