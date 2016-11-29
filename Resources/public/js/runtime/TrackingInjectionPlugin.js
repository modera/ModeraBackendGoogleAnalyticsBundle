/**
 * Adds Google Analytics to MJR so every time a new section/activity is being opened a screenview is sent
 * to GA. This plugin also adds a basic exception logging functionality.
 *
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
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
    constructor: function (config) {
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

        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        //ga('create', this.trackingCode, 'auto');
        ga('create', me.config['tracking_code'], {
            allowAnchor: true,
            userId: me.config['user_id'],
            appName: me.config['app_name'],
            appVersion: me.config['app_version'],
            siteSpeedSampleRate: 100, // GA wil gather timing analytics for 100% of users
        });

        var debugStatus = 'debug ' + (me.config['is_debug'] ? 'enabled' : 'disabled');

        console.debug(
            '%s.bootstrap(): Injected GA tracking snippet with code "%s" (%s).', me.$className, me.config['tracking_code'], debugStatus
        );
    },

    // private
    compileToken: function(sectionName, activities) {
        return [this.config['prefix'], sectionName].concat(activities).join('/');
    },

    // internal
    // also used by "Modera.backend.googleanalytics.profiling.GABackend"
    createToken: function() {
        var sectionName = this.executionContext.getSectionName();
        if (!sectionName) {
            // MPFE-865
            // This local fix is required before MPFE-865 is resolved properly on MJR level
            if (this.rootConfig.menuItems[0]) {
                sectionName = this.rootConfig.menuItems[0].id;
            }
        }

        var token = this.compileToken(
            sectionName,
            Ext.Object.getKeys(this.executionContext.getAllParams())
        );

        return token;
    },

    // private
    logScreenView: function() {
        var token = this.createToken();

        ga('send', 'screenview', {
            'screenName': token
        });

        if (this.config['is_debug']) {
            console.debug('%s: Sent "screenview" hit with screenName "%s".', this.$className, token);
        }
    },

    // override
    bootstrap: function(cb) {
        var me = this;

        this.configProvider.getConfig(function(config) {
            if (!config.hasOwnProperty('modera_backend_google_analytics')) {
                console.error('%s.bootstrap(): Unable to find required config, aborting tracker initialization.', me.$className);

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
                var position = ['f:'+url, 'l:'+line];
                if (col) {
                    position.push('c:'+col);
                }

                var formattedMsg = msg+' ['+position.join(' ; ')+']';

                ga('send', 'exception', {
                    exDescription: formattedMsg
                });
            };

            me.executionContext.on('transactioncommitted', function() {
                me.logScreenView();
            });

            cb();
        });
    }
});
