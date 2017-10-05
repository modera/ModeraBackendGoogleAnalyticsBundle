<?php

namespace Modera\BackendGoogleAnalyticsBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Contributes a plugin to MJR that would allow to track page views.
 *
 * @internal
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ClientDiServiceDefinitionsProvider implements ContributorInterface
{
    /** @var boolean $enabled */
    private $enabled;

    /**
     * @param $enabled
     */
    public function __construct($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $items = array(
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
            'modera_backend_google_analytics.profiling.ga_backend' => array(
                'className' => 'Modera.backend.googleanalytics.profiling.GABackend',
                'args' => [
                    array(
                        'trackingPlugin' => '@modera_backend_google_analytics.runtime.tracking_injection_plugin',
                    ),
                ],
                'tags' => [
                    'profiler_backend',
                ],
            ),
            'activity_profiling_auto_start_plugin' => array(
                'className' => 'MF.profiling.ActivityProfilingAutoStartPlugin',
                'args' => [
                    array(
                        'workbench' => '@workbench',
                        'profiler' => '@profiler',
                    ),
                ],
                'tags' => [
                    'runtime_plugin',
                ],
            ),
        );

        if (!$this->enabled) {
            $items = array();
        }

        return $items;
    }
}
