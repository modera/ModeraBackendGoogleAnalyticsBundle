<?php

namespace Modera\BackendGoogleAnalyticsBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ClassLoaderMappingsProvider implements ContributorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return array(
            'Modera.backend.googleanalytics' => '/bundles/moderabackendgoogleanalytics/js',
        );
    }
}
