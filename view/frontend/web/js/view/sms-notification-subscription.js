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
    'Linkmobility_Notifications/js/model/sms-subscription-preferences-modal'
], function ($, ko, Component, $t, smsNotifications, subscriptionPreferencesModal) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Linkmobility_Notifications/sms-notification-subscription',
            checkboxSelector: '#sms-notifications-subscribed',
            selectedSmsTypes: ko.observable('')
        },
        initObservable: function () {
            this._super();

            smsNotifications.isSubscribed.subscribe(this.handleSubscribe, this);
            smsNotifications.selectedSmsTypes.subscribe(this.setSmsSelectedTypes, this);

            return this;
        },
        handleCheckboxClick: function (data, event) {
            if (!$(event.target).is(':checked')) {
                return true;
            }

            smsNotifications.isSubscribing(true);
        },
        handlePreferencesClick: function () {
            subscriptionPreferencesModal.open(true);
        },
        handleSubscribe: function (isSubscribed) {
            $(this.checkboxSelector).prop('checked', isSubscribed);
        },
        setSmsSelectedTypes: function (selectedSmsTypes) {
            this.selectedSmsTypes(selectedSmsTypes);
        }
    });
});
