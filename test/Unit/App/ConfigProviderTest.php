<?php

declare(strict_types=1);

namespace LightTest\Unit\App;

use Light\App\ConfigProvider;
use Light\App\Handler\GetIndexViewHandler;
use LightTest\Unit\UnitTest;

/**
 * @phpstan-import-type ConfigType from ConfigProvider
 */
class ConfigProviderTest extends UnitTest
{
    /**
     * @phpstan-var ConfigType $config
     * @phpstan-ignore property.defaultValue
     */
    protected array $config = [];

    protected function setup(): void
    {
        parent::setUp();

        $this->config = (new ConfigProvider())();
    }

    public function testConfigHasDependencies(): void
    {
        $this->assertArrayHasKey('dependencies', $this->config);
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertArrayHasKey('factories', $this->config['dependencies']);
        $this->assertIsArray($this->config['dependencies']['factories']);
        $this->assertArrayHasKey(GetIndexViewHandler::class, $this->config['dependencies']['factories']);
    }

    public function testConfigHasTemplates(): void
    {
        $this->assertArrayHasKey('templates', $this->config);
    }

    public function testGetTemplates(): void
    {
        $this->assertArrayHasKey('paths', $this->config['templates']);
        $this->assertIsArray($this->config['templates']['paths']);
        $this->assertArrayHasKey('app', $this->config['templates']['paths']);
        $this->assertArrayHasKey('error', $this->config['templates']['paths']);
        $this->assertArrayHasKey('layout', $this->config['templates']['paths']);
        $this->assertArrayHasKey('partial', $this->config['templates']['paths']);
    }
}
