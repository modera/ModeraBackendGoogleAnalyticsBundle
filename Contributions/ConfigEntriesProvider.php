<?php

namespace Modera\BackendGoogleAnalyticsBundle\Contributions;

use Modera\BackendGoogleAnalyticsBundle\ModeraBackendGoogleAnalyticsBundle;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Modera\ConfigBundle\Config\ConfigurationEntryDefinition as CED;
use Modera\FoundationBundle\Translation\T;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ConfigEntriesProvider implements ContributorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $serverConfig = array(
            'id' => 'modera_config.as_is_handler',
        );

        return [
            new CED(
                ModeraBackendGoogleAnalyticsBundle::TRACKING_CODE_CONFIG_KEY,
                T::trans('Backend tracking code'),
                '',
                'google-analytics',
                $serverConfig,
                array()
            ),
        ];
    }
}
