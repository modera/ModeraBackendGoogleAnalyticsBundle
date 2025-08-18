/**
 * Adds Google Analytics to MJR so every time a new section/activity is being opened a screenview is sent
 * to GA. This plugin also adds a basic exception logging functionality.
 */
Ext.define('Modera.backend.googleanalytics.runtime.TrackingInjectionPlugin', {
    extend: 'MF.runtime.extensibility.AbstractPlugin',

    requires: [
        'MF.Util'
    ],

    /**
     * This service will be used to get trackingCode, prefix and status if debug is enabled.
     *
     * @private
     * @cfg {MF.runtime.config.ConfigProviderInterface} configProvider
     */

    /**
     * @private
     * @cfg {MF.activation.executioncontext.AbstractContext} executionContext
     */

    /**
     * This property is initialized when {@link #bootstrap} method is invoked.
     *
     * @private
     * @property {Object} config
     */

    /**
     * @private
     * @property {Object} rootConfig
     */

    /**
     * @param {Object} config
     */
    constructor: function(config) {
        MF.Util.validateRequiredConfigParams(this, config, ['configProvider', 'executionContext']);

        Ext.apply(this, config);
    },

    // override
    getId: function() {
        return 'modera_backend_google_analytics.tracking_injection_plugin';
    },

    // private
    injectTracker: function() {
        var me = this;

        me.gtag = (function(trackingId, config, dataLayer, fnName) {
            dataLayer = dataLayer || 'dataLayer';
            fnName = fnName || 'gtag';

            window[dataLayer] = window[dataLayer] || [];
            window[fnName] = function gtag() {
                if (me.config['is_debug']) {
                    console.log('[%s] gtag: ', me.$className, arguments);
                }
                window[dataLayer].push(arguments);
            };
            window[fnName]['dataLayer'] = dataLayer;

            window[fnName]('js', new Date());
            window[fnName]('config', trackingId, config);

            var script = document.createElement('script');
            script.async = true;
            script.src = 'https://www.googletagmanager.com/gtag/js?id=' + trackingId + '&l=' + dataLayer;

            var scripts = document.getElementsByTagName('script');
            scripts[0].parentNode.insertBefore(script, scripts[0]);

            return window[fnName];
        })(me.config['tracking_code'], {
            send_page_view: false,
            user_id: me.config['user_id'],
            user_properties: Ext.apply({
                app_name: me.config['app_name'],
                app_version: me.config['app_version']
            }, me.config['user_properties'] || {}),
            site_speed_sample_rate: 100 // GA wil gather timing analytics for 100% of users
        }, me.config['data_layer'], me.config['fn_name']);

        var debugStatus = 'debug ' + (me.config['is_debug'] ? 'enabled' : 'disabled');

        console.debug(
            '%s.injectTracker(): Injected GA tracking snippet with code "%s" (%s).',
            me.$className,
            me.config['tracking_code'],
            debugStatus
        );
    },

    // private
    compileToken: function(sectionName, activities) {
        return [this.config['prefix'], sectionName].concat(activities).join('/');
    },

    // internal
    // also used by "Modera.backend.googleanalytics.profiling.GABackend"
    createToken: function(activity) {
        var me = this;

        var sectionName = me.executionContext.getSectionName();
        if (!sectionName) {
            // MPFE-865
            // This local fix is required before MPFE-865 is resolved properly on MJR level
            if (me.rootConfig.menuItems[0]) {
                sectionName = me.rootConfig.menuItems[0].id;
            }
        }

        var activities = Ext.Object.getKeys(me.executionContext.getAllParams());
        if (activity) {
            var index = activities.indexOf(activity.getId());
            activities.splice(index + 1, Number.POSITIVE_INFINITY);
        }

        return me.compileToken(sectionName, activities);
    },

    // private
    getPageView: function() {
        var me = this;

        return {
            page_title: me.createToken(),
            page_location: window.location.href
        };
    },

    // private
    logScreenView: function() {
        var me = this;

        var pageView = me.getPageView();

        me.gtag && me.gtag('event', 'page_view', pageView);

        if (me.config['is_debug']) {
            console.debug('%s: Sent "page_view" hit:', me.$className, pageView);
        }
    },

    // public
    logException: function(description, fatal) {
        var me = this;

        me.gtag && me.gtag('event', 'exception', Ext.apply({
            description: description,
            fatal: fatal || false
        }, me.getPageView()));
    },

    // override
    bootstrap: function(cb) {
        var me = this;

        me.configProvider.getConfig(function(config) {
            if (!config.hasOwnProperty('modera_backend_google_analytics')) {
                console.error(
                    '%s.bootstrap(): Unable to find required config, aborting tracker initialization.',
                    me.$className
                );

                cb();
                return;
            }

            me.rootConfig = config;
            config = config['modera_backend_google_analytics'];
            me.config = config;

            if (!config['tracking_code']) {
                console.warn(
                    '%s.bootstrap(): No tracking code is specified yet, aborting tracker initialization.',
                    me.$className
                );

                cb();
                return;
            }

            me.injectTracker();

            // MPFE-873
            window.onerror = function(msg, url, line, col, error) {
                var description = msg;
                if (url) {
                    var position = [ 'f:' + url, 'l:' + line ];
                    if (col) {
                        position.push('c:' + col);
                    }

                    description = Ext.String.format('{0} [{1}]', msg, position.join(' ; '));
                }

                me.logException(description);
            };

            me.executionContext.on('transactioncommitted', function() {
                me.logScreenView();
            });

            cb();
        });
    }
});
