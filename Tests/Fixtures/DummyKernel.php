<?php

namespace Modera\BackendGoogleAnalyticsBundle\Tests\Fixtures;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class DummyKernel extends Kernel
{
    public static $appName;

    public static $appVersion;

    public function registerBundles()
    {
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }

    public static function getAppName()
    {
        return self::$appName;
    }

    public static function getAppVersion()
    {
        return self::$appVersion;
    }
}
