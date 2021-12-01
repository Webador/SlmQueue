<?php

namespace App;

use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [],
        ];
    }
}
