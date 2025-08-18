<?php

namespace Modera\BackendGoogleAnalyticsBundle\Tests\Unit\DependencyInjection;

use Modera\BackendGoogleAnalyticsBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * @copyright 2017 Modera Foundation
 */
class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    public function testNoExplicitConfigProvided(): void
    {
        $configuration = new Configuration();

        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, []);

        $this->assertArrayHasKey('enabled', $config);
        $this->assertTrue($config['enabled']);
    }

    public function testWithConfigGiven(): void
    {
        $configuration = new Configuration();

        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, [
            'modera_backend_google_analytics' => [
                'enabled' => false,
            ],
        ]);

        $this->assertArrayHasKey('enabled', $config);
        $this->assertFalse($config['enabled']);
    }
}
