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
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($, ko, modal, $t) {
    'use strict';

    return {
        open: ko.observable(false).extend({notify: 'always'}),
        modalWindow: null,
        /**
         * Create pop-up window for provided element.
         *
         * @param {HTMLElement} element
         * @param {string} title
         * @param {function(Object)} onClose
         */
        createModal: function (element, title, onClose) {
            this.modalWindow = element;

            const options = {
                title: title,
                modalClass: 'modal-popup-sms-subscription-preferences',
                responsive: true,
                buttons: [
                    {
                        text: $t('Save'),
                        class: 'action primary',
                        /** @inheritdoc */
                        click: function () {
                            if (onClose && typeof onClose === 'function') {
                                onClose(this);
                            }
                            
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
