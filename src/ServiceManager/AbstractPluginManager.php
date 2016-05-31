<?php

namespace SlmQueue\ServiceManager;

use Zend\ServiceManager\AbstractPluginManager as ZendAbstractPluginManager;
use Zend\Stdlib\DispatchableInterface as Dispatchable;
use Zend\Mvc\Controller\Plugin\PluginInterface;

/**
 * AbstractPluginManager
 */
abstract class AbstractPluginManager extends ZendAbstractPluginManager implements PluginInterface
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
