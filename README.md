# ModeraBackendGoogleAnalyticsBundle

[![Build Status](https://travis-ci.org/modera/ModeraBackendGoogleAnalyticsBundle.svg?branch=master)](https://travis-ci.org/modera/ModeraBackendGoogleAnalyticsBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/modera/ModeraBackendGoogleAnalyticsBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/modera/ModeraBackendGoogleAnalyticsBundle/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8f68d217-fe94-4c43-9103-551fcf9ddb94/mini.png)](https://insight.sensiolabs.com/projects/8f68d217-fe94-4c43-9103-551fcf9ddb94)

Provides support for gathering 'pageview' analytics in backend using Google Analytics.

## Installation

Add a dependency to your composer.json by running:

    composer require modera/backend-google-analytics-bundle

You don't have to manually update your AppKernel class if you have `modera/module-bundle` bundle installed already, otherwise
you need to add this to your AppKernel:

    new \Modera\BackendGoogleAnalyticsBundle\ModeraBackendGoogleAnalyticsBundle(),

After bundle has been enabled to make sure that contributed configuration properties are installed run this command:

    modera:config:install-config-entries

When this command is executed you will either see that some configuration property(properties) were installed or
no configuration properties were installed, in latter case it means that they were automatically installed by
module-bundle for you during bundle installation process.

# Documentation

If you want to have a UI to access configuration properties contributed by this bundle then you may also want
to install `modera/backend-google-analytics-config-bundle` bundle.

## Application related data

Your kernel class (app/AppKernel.php) might additionally implement two methods: **getAppName**, **getAppVersion** which
then will be used by \Modera\BackendGoogleAnalyticsBundle\Contributions\ConfigMergersProvider to send more
detailed data to GA.

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE
