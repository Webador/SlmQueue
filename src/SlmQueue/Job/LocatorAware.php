<?php

namespace SlmQueue\Job;

use Zend\Di\Locator;

interface LocatorAware extends Job
{
    public function setLocator (Locator $locator);
}
