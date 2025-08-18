<?php

namespace Modera\BackendGoogleAnalyticsBundle\Tests\Unit\Contributions;

use Modera\BackendGoogleAnalyticsBundle\Contributions\ConfigEntriesProvider;
use Modera\BackendGoogleAnalyticsBundle\ModeraBackendGoogleAnalyticsBundle;
use Modera\ConfigBundle\Config\ConfigurationEntryDefinition;

/**
 * @copyright 2016 Modera Foundation
 */
class ConfigEntriesProviderTest extends \PHPUnit\Framework\TestCase
{
    public function testGetItems(): void
    {
        $provider = new ConfigEntriesProvider();

        /** @var ConfigurationEntryDefinition[] $items */
        $items = $provider->getItems();

        $this->assertEquals(1, \count($items));
        $this->assertInstanceOf(ConfigurationEntryDefinition::class, $items[0]);

        $this->assertEquals(ModeraBackendGoogleAnalyticsBundle::TRACKING_CODE_CONFIG_KEY, $items[0]->getName());
    }
}
