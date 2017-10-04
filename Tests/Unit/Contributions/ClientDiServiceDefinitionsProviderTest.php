<?php

namespace Modera\BackendGoogleAnalyticsBundle\Tests\Unit\Contributions;

use Modera\BackendGoogleAnalyticsBundle\Contributions\ClientDiServiceDefinitionsProvider;
use Symfony\Component\DependencyInjection\Container;
/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ClientDiServiceDefinitionsProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItems()
    {
        $provider = new ClientDiServiceDefinitionsProvider(false);

        $items = $provider->getItems();

        $this->assertTrue(is_array($items));
        $this->assertEquals(0, count($items));
    }

    public function testGetItemsIsEnable()
    {
        $provider = new ClientDiServiceDefinitionsProvider(true);

        $items = $provider->getItems();

        $this->assertTrue(is_array($items));
        $this->assertEquals(3, count($items));

        $items = array_values($items);

        $injectionPlugin = $items[0];
        $this->assertTrue(is_array($injectionPlugin));
        $this->assertArrayHasKey('className', $injectionPlugin);
        $this->assertEquals('Modera.backend.googleanalytics.runtime.TrackingInjectionPlugin', $injectionPlugin['className']);
        $this->assertArrayHasKey('args', $injectionPlugin);
        $this->assertTrue(is_array($injectionPlugin['args']));
        $this->assertEquals(1, count($injectionPlugin['args']));
        $arg = $injectionPlugin['args'][0];
        $this->assertArrayHasKey('executionContext', $arg);
        $this->assertEquals('@root_execution_context', $arg['executionContext']);
        $this->assertArrayHasKey('configProvider', $arg);
        $this->assertEquals('@config_provider', $arg['configProvider']);
        $this->assertTrue(in_array('runtime_plugin', $injectionPlugin['tags']));

        $gaBackend = $items[1];
        $this->assertArrayHasKey('className', $gaBackend);
        $this->assertArrayHasKey('tags', $gaBackend);
        $this->assertEquals(['profiler_backend'], $gaBackend['tags']);

        $autoStartPlugin = $items[2];
        $this->assertArrayHasKey('className', $autoStartPlugin);
        $this->assertArrayHasKey('tags', $autoStartPlugin);
        $this->assertEquals(['runtime_plugin'], $autoStartPlugin['tags']);
    }
}
