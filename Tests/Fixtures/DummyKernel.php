<?php

namespace Modera\BackendGoogleAnalyticsBundle\Tests\Fixtures;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @copyright 2016 Modera Foundation
 */
class DummyKernel extends Kernel
{
    public static string $appName = '';

    public static string $appVersion = '';

    public function registerBundles(): iterable
    {
        return [];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
    }

    public static function getAppName(): string
    {
        return self::$appName;
    }

    public static function getAppVersion(): string
    {
        return self::$appVersion;
    }
}
