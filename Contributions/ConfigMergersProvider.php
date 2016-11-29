<?php

namespace Modera\BackendGoogleAnalyticsBundle\Contributions;

use Modera\BackendGoogleAnalyticsBundle\ModeraBackendGoogleAnalyticsBundle;
use Modera\ConfigBundle\Config\ConfigurationEntriesManagerInterface;
use Modera\MjrIntegrationBundle\Config\CallbackConfigMerger;
use Modera\SecurityBundle\Entity\User;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @internal
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ConfigMergersProvider implements ContributorInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var string
     */
    private $env;

    /**
     * @var ConfigurationEntriesManagerInterface
     */
    private $configEntriesManager;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @param TokenStorageInterface                $tokenStorage
     * @param ConfigurationEntriesManagerInterface $configEntriesManager
     * @param KernelInterface                      $kernel
     * @param string                               $env
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        ConfigurationEntriesManagerInterface $configEntriesManager,
        KernelInterface $kernel,
        $env
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->configEntriesManager = $configEntriesManager;
        $this->kernel = $kernel;
        $this->env = $env;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        /* @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $trackingCode = $this->configEntriesManager->findOneByNameOrDie(ModeraBackendGoogleAnalyticsBundle::TRACKING_CODE_CONFIG_KEY);

        $appName = 'Modera Foundation';
        $appVersion = '1.0.0';

        $reflKernel = new \ReflectionObject($this->kernel);
        if ($reflKernel->hasMethod('getAppName')) {
            $appName = $reflKernel->getMethod('getAppName')->invoke($this->kernel);
        }
        if ($reflKernel->hasMethod('getAppVersion')) {
            $appVersion = $reflKernel->getMethod('getAppVersion')->invoke($this->kernel);
        }

        return [
            new CallbackConfigMerger(function (array $currentConfig) use ($trackingCode, $user, $appName, $appVersion) {
                $currentConfig['modera_backend_google_analytics'] = array(
                    'user_id' => $user->getId(),
                    'tracking_code' => $trackingCode->getValue(),
                    'is_debug' => 'prod' != $this->env,
                    'prefix' => '/backend', // TODO use %modera_mjr_integration.routes_prefix% instead ?
                    'app_name' => $appName,
                    'app_version' => $appVersion,
                );

                return $currentConfig;
            }),
        ];
    }
}
