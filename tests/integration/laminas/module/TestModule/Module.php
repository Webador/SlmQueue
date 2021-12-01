<?php

namespace TestModule;

use Laminas\ModuleManager\Feature;

class Module implements Feature\ConfigProviderInterface
{
    public function getConfig()
    {
        return [

        ];
    }
}
