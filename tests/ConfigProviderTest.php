<?php

namespace SlmQueueTest;

use PHPUnit\Framework\TestCase as TestCase;
use SlmQueue\ConfigProvider;
use SlmQueue\Module;

class ConfigProviderTest extends TestCase
{
    public function testConfigProviderGetConfig()
    {
        $configProvider = new ConfigProvider();
        $config = $configProvider();

        static::assertNotEmpty($config);
    }

    public function testConfigEqualsToModuleConfig()
    {
        $module = new Module();
        $moduleConfig = $module->getConfig();
        $configProvider = new ConfigProvider();
        $config = $configProvider();

        static::assertEquals($moduleConfig['service_manager'], $config['dependencies']);
        static::assertEquals($moduleConfig['slm_queue'], $config['slm_queue']);
    }
}
