<?php

namespace Modera\BackendGoogleAnalyticsBundle\Tests\Unit\DependencyInjection;

use Modera\BackendGoogleAnalyticsBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author  Alexander Ivanitsa <alexander.ivanitsa@modera.net>
 * @copyright 2017 Modera Foundation
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testNoExplicitConfigProvided()
    {
        $configuration = new Configuration();

        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, array());

        $this->assertArrayHasKey('enabled', $config);
        $this->assertTrue($config['enabled']);
    }

    public function testWithConfigGiven()
    {
        $configuration = new Configuration();

        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, array(
            'modera_backend_google_analytics' => array(
                'enabled' => false,
            ),
        ));

        $this->assertArrayHasKey('enabled', $config);
        $this->assertFalse($config['enabled']);
    }
}
