/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

define([
    'ko',
    'uiComponent',
    'mage/translate',
    'Wagento_SMSNotifications/js/model/sms-notifications'
], function (ko, Component, $t, smsNotifications) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Wagento_SMSNotifications/mobile-telephone-field',
            mobileTelephonePrefixOptions: [],
            defaultMobileTelephonePrefix: '',
            mobileTelephonePrefix: '',
            mobileTelephoneNumber: '',
            tracks: {
                mobileTelephonePrefix: true,
                mobileTelephoneNumber: true
            },
            statefull: {
                mobileTelephonePrefix: true,
                mobileTelephoneNumber: true
            }
        },
        showField: ko.observable(false),
        initialize: function () {
            this._super();

            if (this.mobileTelephonePrefix.length === 0 && this.defaultMobileTelephonePrefix.length > 0) {
                this.mobileTelephonePrefix = this.defaultMobileTelephonePrefix;
            }
        },
        initObservable: function () {
            this._super();

            smsNotifications.isSubscribed.subscribe(this.toggleFieldVisibility, this);

            return this;
        },
        toggleFieldVisibility: function (isSubscribed) {
            this.showField(isSubscribed);

            if (!isSubscribed) {
                this.mobileTelephonePrefix = this.defaultMobileTelephonePrefix;
                this.mobileTelephoneNumber = '';
            }
        }
    });
});
