<?php

namespace Modera\BackendGoogleAnalyticsBundle\Contributions;

use Modera\ExpanderBundle\Ext\ContributorInterface;

/**
 * Contributes a plugin to MJR that would allow to track page views.
 *
 * @internal
 *
 * @copyright 2016 Modera Foundation
 */
class ClientDiServiceDefinitionsProvider implements ContributorInterface
{
    public function __construct(
        private readonly bool $enabled = false,
    ) {
    }

    public function getItems(): array
    {
        if (!$this->enabled) {
            return [];
        }

        $items = [
            'modera_backend_google_analytics.runtime.tracking_injection_plugin' => [
                'className' => 'Modera.backend.googleanalytics.runtime.TrackingInjectionPlugin',
                'args' => [
                    [
                        'executionContext' => '@root_execution_context',
                        'configProvider' => '@config_provider',
                    ],
                ],
                'tags' => ['runtime_plugin'],
            ],
            'modera_backend_google_analytics.profiling.ga_backend' => [
                'className' => 'Modera.backend.googleanalytics.profiling.GABackend',
                'args' => [
                    [
                        'trackingPlugin' => '@modera_backend_google_analytics.runtime.tracking_injection_plugin',
                    ],
                ],
                'tags' => [
                    'profiler_backend',
                ],
            ],
            'activity_profiling_auto_start_plugin' => [
                'className' => 'MF.profiling.ActivityProfilingAutoStartPlugin',
                'args' => [
                    [
                        'workbench' => '@workbench',
                        'profiler' => '@profiler',
                    ],
                ],
                'tags' => [
                    'runtime_plugin',
                ],
            ],
        ];

        return $items;
    }
}
