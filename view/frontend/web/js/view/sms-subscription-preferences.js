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
    'Linkmobility_Notifications/js/model/sms-subscription-preferences-modal'
], function ($, Component, subscriptionPreferencesModal) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Linkmobility_Notifications/sms-subscription-preferences',
            groupedSmsTypes: {},
            modalTriggerSelector: '[data-role="sms-subscription-preferences-modal-trigger"]',
            modalTitle: null
        },
        modalTrigger: null,
        initialize: function () {
            this._super();
            this.modalTrigger = $(this.modalTriggerSelector);

            $(this.modalTrigger).on('click', $.proxy(this.showModal, this));
        },
        initModal: function(element) {
            subscriptionPreferencesModal.createModal(element, this.modalTitle, this.hideModal);
        },
        showModal: function (event) {
            subscriptionPreferencesModal.showModal();

            event.preventDefault();
        },
        hideModal: function (context) {
            let smsTypes = [];

            $(context.element).find('.sms-type-checkbox:checked').each(function () {
                smsTypes.push($(this).val());
            });

            $('#sms-notifications-sms-types').val(smsTypes.join(','));
        },
        selectAll: function () {
            $('.sms-type-checkbox').each(function () {
                $(this).prop('checked', $('#sms-type-select-all').prop('checked'));
            });

            return true;
        },
        toggleSelectAll: function () {
            const $checkboxes = $('.sms-type-checkbox');

            $('#sms-type-select-all').prop('checked', $checkboxes.filter(':checked').length === $checkboxes.length);

            return true;
        }
    });
});
