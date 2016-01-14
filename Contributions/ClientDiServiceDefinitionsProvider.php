<?php

namespace Modera\BackendGoogleAnalyticsBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Contributes a plugin to MJR that would allow to track page views.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ClientDiServiceDefinitionsProvider implements ContributorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return array(
            'modera_backend_google_analytics.runtime.tracking_injection_plugin' => array(
                'className' => 'Modera.backend.googleanalytics.runtime.TrackingInjectionPlugin',
                'args' => [
                    array(
                        'executionContext' => '@root_execution_context',
                        'configProvider' => '@config_provider',
                    ),
                ],
                'tags' => ['runtime_plugin'],
            ),
        );
    }
}
