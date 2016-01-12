<?php

namespace Modera\BackendGoogleAnalyticsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ModeraBackendGoogleAnalyticsBundle extends Bundle
{
    // see \Modera\BackendGoogleAnalyticsBundle\Contributions\ConfigEntriesProvider
    const TRACKING_CODE_CONFIG_KEY = 'modera_backend_google_analytics.tracking_code';
}
