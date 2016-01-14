<?php

namespace Modera\BackendGoogleAnalyticsBundle\Contributions;

use Modera\BackendGoogleAnalyticsBundle\ModeraBackendGoogleAnalyticsBundle;
use Modera\ConfigBundle\Config\ConfigurationEntriesManagerInterface;
use Modera\MjrIntegrationBundle\Config\CallbackConfigMerger;
use Modera\SecurityBundle\Entity\User;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
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
     * @param TokenStorageInterface                $tokenStorage
     * @param ConfigurationEntriesManagerInterface $configEntriesManager
     * @param string                               $env
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        ConfigurationEntriesManagerInterface $configEntriesManager,
        $env
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->configEntriesManager = $configEntriesManager;
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

        return [
            new CallbackConfigMerger(function (array $currentConfig) use ($trackingCode, $user) {
                $currentConfig['modera_backend_google_analytics'] = array(
                    'user_id' => $user->getId(),
                    'tracking_code' => $trackingCode->getValue(),
                    'is_debug' => 'prod' != $this->env,
                    'prefix' => '/backend',
                );

                return $currentConfig;
            }),
        ];
    }
}
