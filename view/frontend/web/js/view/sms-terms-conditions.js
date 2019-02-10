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
    'uiComponent',
    'Linkmobility_Notifications/js/model/sms-terms-conditions-modal',
    'smsNotifications'
], function ($, Component, termsConditionsModal, smsNotifications) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Linkmobility_Notifications/sms-terms-conditions',
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
