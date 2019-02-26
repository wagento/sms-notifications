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
    'jquery',
    'ko',
    'uiComponent',
    'mage/translate',
    'smsNotifications',
    'LinkMobility_SMSNotifications/js/model/sms-subscription-preferences-modal',
    'LinkMobility_SMSNotifications/js/model/sms-terms-conditions-modal'
], function ($, ko, Component, $t, smsNotifications, subscriptionPreferencesModal, termsConditionsModal) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'LinkMobility_SMSNotifications/sms-notification-subscription',
            isOptinRequired: true,
            selectedSmsTypes: ko.observable('')
        },
        isSubscribeChecked: ko.observable(false),
        initialize: function () {
            this._super();

            if (!this.isOptinRequired) {
                this.isSubscribeChecked(true);

                smsNotifications.isSubscribed(true);
            }
        },
        initObservable: function () {
            this._super();

            smsNotifications.isSubscribed.subscribe(this.handleSubscribe, this);
            smsNotifications.selectedSmsTypes.subscribe(this.setSmsSelectedTypes, this);

            return this;
        },
        handleCheckboxClick: function (data, event) {
            if (!$(event.target).is(':checked')) {
                smsNotifications.isSubscribing(false);
                smsNotifications.isSubscribed(false);

                return true;
            }

            if (data.isOptinRequired) {
                smsNotifications.isSubscribing(true);

                event.stopImmediatePropagation();

                return false;
            }

            smsNotifications.isSubscribing(false);
            smsNotifications.isSubscribed(true);

            return true;
        },
        handlePreferencesClick: function () {
            subscriptionPreferencesModal.open(true);
        },
        handleTermsConditionsClick: function () {
            termsConditionsModal.showModal();
        },
        handleSubscribe: function (isSubscribed) {
            this.isSubscribeChecked(isSubscribed);
        },
        setSmsSelectedTypes: function (selectedSmsTypes) {
            this.selectedSmsTypes(selectedSmsTypes.join(','));
        }
    });
});
