<?php

namespace SlmQueueTest;

use SlmQueueTest\Module;
use PHPUnit_Framework_TestCase as TestCase;

class ConfigProviderTest extends TestCase
{
public function testConfigProviderGetConfig()
{
$configProvider = new \SlmQueue\ConfigProvider();
$config         = $configProvider();

$this->assertNotEmpty($config);
}

public function testConfigEqualsToModuleConfig()
{
$module         = new Module();
$moduleConfig   = $module->getConfig();
$configProvider = new \SlmQueue\ConfigProvider();
$config         = $configProvider();

$this->assertEquals($moduleConfig['service_manager'], $config['dependencies']);
$this->assertEquals($moduleConfig['slm_queue'], $config['slm_queue']);
}
}