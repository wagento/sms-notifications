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
    'LinkMobility_SMSNotifications/js/model/sms-subscription-preferences-modal',
    'smsNotifications'
], function ($, Component, subscriptionPreferencesModal, smsNotifications) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'LinkMobility_SMSNotifications/sms-subscription-preferences',
            groupedSmsTypes: {},
            modalTitle: null
        },
        modalTrigger: null,
        initObservable: function () {
            this._super();

            subscriptionPreferencesModal.open.subscribe(this.showModal);

            return this;
        },
        initModal: function(element) {
            subscriptionPreferencesModal.createModal(element, this.modalTitle, this.hideModal);
        },
        showModal: function () {
            subscriptionPreferencesModal.showModal();
        },
        hideModal: function (context) {
            smsNotifications.selectedSmsTypes.removeAll();

            $(context.element).find('.sms-type-checkbox:checked').each(function () {
                smsNotifications.selectedSmsTypes.push($(this).val());
            });
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
