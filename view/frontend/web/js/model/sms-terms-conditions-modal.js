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
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'Linkmobility_Notifications/js/model/sms-notifications'
], function ($, modal, $t, smsNotifications) {
    'use strict';

    return {
        modalWindow: null,
        /**
         * Create pop-up window for provided element.
         *
         * @param {HTMLElement} element
         * @param {string} title
         */
        createModal: function (element, title) {
            this.modalWindow = element;

            const options = {
                title: title,
                modalClass: 'modal-popup-sms-terms-conditions',
                responsive: true,
                buttons: [
                    {
                        text: $t('I Agree'),
                        class: 'action primary',
                        click: function () {
                            smsNotifications.isSubscribed(true);
                            smsNotifications.isSubscribing(false);

                            this.closeModal();
                        }
                    },
                    {
                        text: $t('Cancel'),
                        class: 'action secondary',
                        click: function () {
                            smsNotifications.isSubscribed(false);
                            smsNotifications.isSubscribing(false);

                            this.closeModal();
                        }
                    }
                ]
            };

            modal(options, $(this.modalWindow));
        },
        showModal: function () {
            $(this.modalWindow).modal('openModal');
        }
    };
});
