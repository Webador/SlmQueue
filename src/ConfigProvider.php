<?php

namespace SlmQueue;

class ConfigProvider
{
    public function __invoke()
    {
        $module = new Module();
        $config = $module->getConfig();

        return [
            'dependencies' => $config['service_manager'],
            'slm_queue' => $config['slm_queue'],
        ];
    }
}
