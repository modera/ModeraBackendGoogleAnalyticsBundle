<?php

namespace Modera\BackendGoogleAnalyticsBundle\Contributions;

use Modera\BackendGoogleAnalyticsBundle\ModeraBackendGoogleAnalyticsBundle;
use Modera\ConfigBundle\Config\ConfigurationEntryDefinition as CED;
use Modera\FoundationBundle\Translation\T;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @internal
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ConfigEntriesProvider implements ContributorInterface
{
    public function getItems(): array
    {
        $serverConfig = [
            'id' => 'modera_config.as_is_handler',
        ];

        return [
            new CED(
                ModeraBackendGoogleAnalyticsBundle::TRACKING_CODE_CONFIG_KEY,
                T::trans('Backend tracking code'),
                '',
                'google-analytics',
                $serverConfig,
                []
            ),
        ];
    }
}
