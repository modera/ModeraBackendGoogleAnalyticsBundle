<?php

namespace Modera\BackendGoogleAnalyticsBundle\Tests\Unit\Contributions;

use Modera\BackendGoogleAnalyticsBundle\Contributions\ClientDiServiceDefinitionsProvider;
use Modera\BackendGoogleAnalyticsBundle\ModeraBackendGoogleAnalyticsBundle;
use Modera\SecurityBundle\Entity\User;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ClientDiServiceDefinitionsProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItems()
    {
        $user = \Phake::mock(User::clazz());
        \Phake::when($user)
            ->getId()
            ->thenReturn('foo-id')
        ;

        $token = \Phake::mock('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken');
        \Phake::when($token)
            ->getUser()
            ->thenReturn($user)
        ;

        $tokenStorage = \Phake::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        \Phake::when($tokenStorage)
            ->getToken()
            ->thenReturn($token)
        ;

        $configEntry = \Phake::mock('Modera\ConfigBundle\Config\ConfigurationEntryInterface');
        \Phake::when($configEntry)
            ->getValue()
            ->thenReturn('foo-value')
        ;

        $configEntriesManager = \Phake::mock('Modera\ConfigBundle\Config\ConfigurationEntriesManagerInterface');
        \Phake::when($configEntriesManager)
            ->findOneByNameOrDie(ModeraBackendGoogleAnalyticsBundle::TRACKING_CODE_CONFIG_KEY)
            ->thenReturn($configEntry)
        ;

        $provider = new ClientDiServiceDefinitionsProvider($tokenStorage, $configEntriesManager, 'prod');

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
        $this->assertArrayHasKey('trackingCode', $arg);
        $this->assertEquals('foo-value', $arg['trackingCode']);
        $this->assertEquals('foo-id', $arg['userId']);
        $this->assertArrayHasKey('userId', $arg);
        $this->assertArrayHasKey('isDebug', $arg);
        $this->assertFalse($arg['isDebug']); // because it's "prod" env now
        $this->assertArrayHasKey('tags', $service);
        $this->assertTrue(in_array('runtime_plugin', $service['tags']));

        // --- env = dev

        $provider = new ClientDiServiceDefinitionsProvider($tokenStorage, $configEntriesManager, 'dev');

        $items = $provider->getItems();

        $this->assertTrue(is_array($items));
        $this->assertEquals(1, count($items));

        $items = array_values($items);
        $service = $items[0];

        $this->assertTrue($service['args'][0]['isDebug']);
    }
}
