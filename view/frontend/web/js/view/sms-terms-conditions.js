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
    'jquery',
    'uiComponent',
    'Wagento_SMSNotifications/js/model/sms-terms-conditions-modal',
    'Wagento_SMSNotifications/js/model/sms-notifications'
], function ($, Component, termsConditionsModal, smsNotifications) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Wagento_SMSNotifications/sms-terms-conditions',
            modalTitle: null,
            modalContent: null
        },
        initObservable: function () {
            this._super();

            smsNotifications.isSubscribing.subscribe(this.showModal);

            return this;
        },
        initModal: function(element) {
            termsConditionsModal.createModal(element, this.modalTitle);
        },
        showModal: function () {
            if (!smsNotifications.isSubscribing()) {
                return;
            }

            termsConditionsModal.showModal();
        }
    });
});
