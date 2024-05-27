<?php

namespace Modera\BackendGoogleAnalyticsBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @internal
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ClassLoaderMappingsProvider implements ContributorInterface
{
    public function getItems(): array
    {
        return [
            'Modera.backend.googleanalytics' => '/bundles/moderabackendgoogleanalytics/js',
        ];
    }
}
