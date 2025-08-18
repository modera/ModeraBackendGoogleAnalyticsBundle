<?php

namespace Modera\BackendGoogleAnalyticsBundle\Contributions;

use Modera\ExpanderBundle\Ext\ContributorInterface;

/**
 * @internal
 *
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
