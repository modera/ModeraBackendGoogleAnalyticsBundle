/**
 * Reports profiling results to Google Analytics. This backend assumes that GA is already properly initialized
 * when the reporting is done.
 *
 * This class requires MJR of version 0.3 or higher.
 *
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.googleanalytics.profiling.GABackend', {
    extend: 'MF.profiling.AbstractBackend',

    requires: [
        'MF.activation.activities.AbstractActivity',
        'MF.Util'
    ],

    /**
     * @cfg {Modera.backend.googleanalytics.runtime.TrackingInjectionPlugin} trackingPlugin
     */

    /**
     * By requirements we should avoid sending identical reports twice, that is - if activity was activated, deactivated
     * and then activated again (exactly in this sequence, so no other activities were activated in between) then
     * then profiling result will be reported only once, the first time.
     *
     * @private
     *
     * @property {mixed} lastProfilerKey
     */

    /**
     * @private
     *
     * @param {Object} config
     */
    constructor: function (config) {
        MF.Util.validateRequiredConfigParams(this, config, ['trackingPlugin']);

        Ext.apply(this, config);

        this.lastProfiledKey = null;
    },

    /**
     * This backend supports "key" formatted in a special way - cat:var:label, which correspond to
     * GA's timingCategory, timingVar and timingLable correspondingly, please take
     * a look at https://developers.google.com/analytics/devguides/collection/analyticsjs/user-timings
     * for more details.
     *
     * @inheritDoc
     */
    onProfileComplete: function(target, key, ms) {
        if (this.lastProfiledKey == key) {
            console.debug(
                '%s.onProfileComplete(target, key, ms): Aborting sending same profiling result twice', this.$className
            );

            return false;
        }

        if (target instanceof MF.activation.activities.AbstractActivity) {
            ga('send', {
                hitType: 'timing',
                timingCategory: 'MJR',
                timingVar: this.trackingPlugin.createToken(),
                timingLabel: 'View activation',
                timingValue: ms
            });
        } else {
            if (key.indexOf(':') != -1) {
                // cat:var:label
                var parts = key.split(":");
                if (parts.length >= 2) {
                    var cfg = {
                        hitType: 'timing',
                        timingCategory: parts[0],
                        timingVar: parts[1],
                        timingValue: ms
                    };

                    if (parts[2]) {
                        cfg.timingLabel = parts[2];
                    }

                    ga('send', cfg);
                }
            }
        }

        this.lastProfiledKey = key;

        return true;
    }
});