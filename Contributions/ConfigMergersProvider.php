<?php

namespace Modera\BackendGoogleAnalyticsBundle\Contributions;

use Modera\BackendGoogleAnalyticsBundle\ModeraBackendGoogleAnalyticsBundle;
use Modera\ConfigBundle\Manager\ConfigurationEntriesManagerInterface;
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
    private TokenStorageInterface $tokenStorage;

    private ConfigurationEntriesManagerInterface $configEntriesManager;

    private KernelInterface $kernel;

    private string $env;

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

    public function getItems(): array
    {
        /** @var User $user */
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
                $currentConfig['modera_backend_google_analytics'] = [
                    'user_id' => $user->getId(),
                    'data_layer' => 'googleTagDataLayer',
                    'fn_name' => 'googleTag',
                    'tracking_code' => $trackingCode->getValue(),
                    'is_debug' => 'prod' != $this->env,
                    'prefix' => '/backend', // TODO use %modera_mjr_integration.routes_prefix% instead ?
                    'app_name' => $appName,
                    'app_version' => $appVersion,
                ];

                return $currentConfig;
            }),
        ];
    }
}
