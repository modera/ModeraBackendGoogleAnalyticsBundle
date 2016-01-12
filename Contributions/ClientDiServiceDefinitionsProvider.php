<?php

namespace Modera\BackendGoogleAnalyticsBundle\Contributions;

use Modera\BackendGoogleAnalyticsBundle\ModeraBackendGoogleAnalyticsBundle;
use Modera\ConfigBundle\Config\ConfigurationEntriesManagerInterface;
use Modera\SecurityBundle\Entity\User;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Contributes a plugin to MJR that would allow to track page views.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ClientDiServiceDefinitionsProvider implements ContributorInterface
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

        return array(
            'modera_backend_google_analytics.runtime.tracking_injection_plugin' => array(
                'className' => 'Modera.backend.googleanalytics.runtime.TrackingInjectionPlugin',
                'args' => [
                    array(
                        'executionContext' => '@root_execution_context',
                        'trackingCode' => $trackingCode->getValue(),
                        'userId' => $user->getId(),
                        'isDebug' => 'prod' != $this->env,
                    ),
                ],
                'tags' => ['runtime_plugin'],
            ),
        );
    }
}
