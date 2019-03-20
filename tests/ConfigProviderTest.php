<?php

namespace SlmQueueTest;

use SlmQueue\Module;
use PHPUnit_Framework_TestCase as TestCase;

class ConfigProviderTest extends TestCase
{
public function testConfigProviderGetConfig()
{
$configProvider = new \SlmQueue\ConfigProvider();
$config         = $configProvider();

static::assertNotEmpty($config);
}

public function testConfigEqualsToModuleConfig()
{
$module         = new Module();
$moduleConfig   = $module->getConfig();
$configProvider = new \SlmQueue\ConfigProvider();
$config         = $configProvider();

static::assertEquals($moduleConfig['service_manager'], $config['dependencies']);
static::assertEquals($moduleConfig['slm_queue'], $config['slm_queue']);
}
}