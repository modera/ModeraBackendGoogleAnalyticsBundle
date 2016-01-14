<?php

namespace Modera\BackendGoogleAnalyticsBundle\Tests\Unit\Contributions;

use Modera\BackendGoogleAnalyticsBundle\Contributions\ConfigMergersProvider;
use Modera\BackendGoogleAnalyticsBundle\ModeraBackendGoogleAnalyticsBundle;
use Modera\MjrIntegrationBundle\Config\ConfigMergerInterface;
use Modera\SecurityBundle\Entity\User;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ConfigMergersProviderTest extends \PHPUnit_Framework_TestCase
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

        $provider = new ConfigMergersProvider($tokenStorage, $configEntriesManager, 'prod');

        $items = $provider->getItems();

        $this->assertTrue(is_array($items));
        $this->assertEquals(1, count($items));

        /* @var ConfigMergerInterface $merger */
        $merger = $items[0];

        $this->assertInstanceOf('Modera\MjrIntegrationBundle\Config\ConfigMergerInterface', $merger);

        $config = $merger->merge(array());

        $this->assertArrayHasKey('modera_backend_google_analytics', $config);
        $config = $config['modera_backend_google_analytics'];
        $this->assertArrayHasKey('tracking_code', $config);
        $this->assertEquals('foo-value', $config['tracking_code']);
        $this->assertEquals('foo-id', $config['user_id']);
        $this->assertArrayHasKey('user_id', $config);
        $this->assertArrayHasKey('is_debug', $config);
        $this->assertFalse($config['is_debug']); // because it's "prod" env now
        $this->assertArrayHasKey('prefix', $config);
        $this->assertEquals('/backend', $config['prefix']);

        // --- env = dev

        $provider = new ConfigMergersProvider($tokenStorage, $configEntriesManager, 'dev');

        $items = $provider->getItems();

        $this->assertTrue(is_array($items));
        $this->assertEquals(1, count($items));

        $config = $items[0]->merge(array());

        $this->assertTrue($config['modera_backend_google_analytics']['is_debug']);
    }
}
