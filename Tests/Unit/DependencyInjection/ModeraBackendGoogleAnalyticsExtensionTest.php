<?php

namespace Modera\BackendGoogleAnalyticsBundle\Tests\Unit\DependencyInjection;

use Modera\BackendGoogleAnalyticsBundle\DependencyInjection\ModeraBackendGoogleAnalyticsExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ModeraBackendGoogleAnalyticsExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $ext = new ModeraBackendGoogleAnalyticsExtension();

        $builder = new ContainerBuilder();

        $ext->load(array(), $builder);

        $this->assertTrue($builder->hasDefinition('modera_backend_google_analytics.contributions.client_di_service_definitions_provider'));
        $this->assertTrue($builder->hasDefinition('modera_backend_google_analytics.contributions.class_loader_mappings_provider'));
        $this->assertTrue($builder->hasDefinition('modera_backend_google_analytics.contributions.config_entries_provider'));

        $clientDefsProvider = $builder->getDefinition('modera_backend_google_analytics.contributions.client_di_service_definitions_provider');
        $this->assertEquals(1, count($clientDefsProvider->getTag('modera_mjr_integration.csdi.service_definitions_provider')));

        $classLoaderMappingProvider = $builder->getDefinition('modera_backend_google_analytics.contributions.class_loader_mappings_provider');
        $this->assertEquals(1, count($classLoaderMappingProvider->getTag('modera_mjr_integration.class_loader_mappings_provider')));

        $configEntriesProvider = $builder->getDefinition('modera_backend_google_analytics.contributions.config_entries_provider');
        $this->assertEquals(1, count($configEntriesProvider->getTag('modera_config.config_entries_provider')));

        $configMergersProvider = $builder->getDefinition('modera_backend_google_analytics.contributions.config_mergers_provider');
        $this->assertEquals(1, count($configMergersProvider->getTag('modera_mjr_integration.config.config_mergers_provider')));
    }
}
