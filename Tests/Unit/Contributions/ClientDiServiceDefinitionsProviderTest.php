<?php

namespace Modera\BackendGoogleAnalyticsBundle\Tests\Unit\Contributions;

use Modera\BackendGoogleAnalyticsBundle\Contributions\ClientDiServiceDefinitionsProvider;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ClientDiServiceDefinitionsProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItems()
    {
        $provider = new ClientDiServiceDefinitionsProvider();

        $items = $provider->getItems();

        $this->assertTrue(is_array($items));
        $this->assertEquals(1, count($items));

        $items = array_values($items);
        $service = $items[0];

        $this->assertTrue(is_array($service));
        $this->assertArrayHasKey('className', $service);
        $this->assertEquals('Modera.backend.googleanalytics.runtime.TrackingInjectionPlugin', $service['className']);
        $this->assertArrayHasKey('args', $service);
        $this->assertTrue(is_array($service['args']));
        $this->assertEquals(1, count($service['args']));
        $arg = $service['args'][0];
        $this->assertArrayHasKey('executionContext', $arg);
        $this->assertEquals('@root_execution_context', $arg['executionContext']);
        $this->assertArrayHasKey('configProvider', $arg);
        $this->assertEquals('@config_provider', $arg['configProvider']);
        $this->assertTrue(in_array('runtime_plugin', $service['tags']));
    }
}
