<?php

namespace SlmQueue\ServiceManager;

use Laminas\Mvc\Controller\Plugin\PluginInterface;
use Laminas\ServiceManager\AbstractPluginManager as LaminasAbstractPluginManager;
use Laminas\Stdlib\DispatchableInterface as Dispatchable;

abstract class AbstractPluginManager extends LaminasAbstractPluginManager implements PluginInterface
{
    /**
     *
     * @var Dispatchable
     */
    protected $controller;

    public function validatePlugin($plugin): void
    {
        $this->validate($plugin);
    }

    public function getController(): Dispatchable
    {
        return $this->controller;
    }

    public function setController(Dispatchable $controller): self
    {
        $this->controller = $controller;

        return $this;
    }
}
