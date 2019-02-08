/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

define([
    'ko',
    'uiComponent',
    'mage/translate',
    'smsNotifications'
], function (ko, Component, $t, smsNotifications) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Linkmobility_Notifications/mobile-telephone-field',
            showField: false,
            mobileTelephonePrefixOptions: [],
            mobileTelephonePrefix: '',
            mobileTelephoneNumber: '',
            tracks: {
                showField: true,
                mobileTelephonePrefix: true,
                mobileTelephoneNumber: true
            }
        },
        defaultMobileTelephonePrefix: '',
        initialize: function () {
            this._super();

            this.defaultMobileTelephonePrefix = this.mobileTelephonePrefix;
        },
        initObservable: function () {
            this._super();

            smsNotifications.isSubscribed.subscribe(this.toggleFieldVisibility, this);

            return this;
        },
        toggleFieldVisibility: function (isSubscribed) {
            this.showField = isSubscribed;

            if (!isSubscribed) {
                this.mobileTelephonePrefix = this.defaultMobileTelephonePrefix;
                this.mobileTelephoneNumber = '';
            }
        }
    });
});
