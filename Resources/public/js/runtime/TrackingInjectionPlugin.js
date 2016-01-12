/**
 * Adds Google Analytics to MJR so every time a new section/activity is being opened a pageview is sent
 * to GA.
 *
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.googleanalytics.runtime.TrackingInjectionPlugin', {
    extend: 'MF.runtime.extensibility.AbstractPlugin',

    requires: [
        'MF.Util'
    ],

    /**
     * @private
     * @property {String} trackingCode
     */

    /**
     * @private
     * @property {String} userId
     */

    /**
     * If set to `TRUE` then every time a pageview is sent to GA then it is going to be
     * logged in JS console as well.
     *
     * @private
     * @property {boolean} isDebug
     */

    /**
     * @private
     * @property {MF.activation.executioncontext.AbstractContext} executionContext
     */

    /**
     * @param {Object} config
     */
    constructor: function (config) {
        MF.Util.validateRequiredConfigParams(this, config, ['trackingCode', 'userId']);

        Ext.apply(this, config);
    },

    // override
    getId: function() {
        return 'modera_backend_google_analytics.tracking_injection_plugin';
    },

    // private
    injectTracker: function() {
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        //ga('create', this.trackingCode, 'auto');
        ga('create', this.trackingCode, {
            allowAnchor: true,
            userId: this.userId
        });

        var debugStatus = 'debug ' + (this.isDebug ? 'enabled' : 'disabled');

        console.debug(
            '%s.bootstrap(): Injected GA tracking snippet with code "%s" (%s).', this.$className, this.trackingCode, debugStatus
        );
    },

    // private
    compileToken: function(sectionName, activities) {
        return [sectionName].concat(activities).join('/');
    },

    // private
    logPageView: function() {
        var token = this.compileToken(
            this.executionContext.getSectionName(),
            Ext.Object.getKeys(this.executionContext.getAllParams())
        );
        ga('send', 'pageview', token);

        if (this.isDebug) {
            console.debug('%s: Sent "pageview" token with "%s".', this.$className, token);
        }
    },

    // override
    bootstrap: function(cb) {
        var me = this;

        this.injectTracker();
        this.logPageView();

        this.executionContext.on('transactioncommitted', function(context, stateObject) {
            me.logPageView();
        });

        cb();
    }
});
