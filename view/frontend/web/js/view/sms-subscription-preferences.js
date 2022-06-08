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
    'ko',
    'uiComponent',
    'Wagento_SMSNotifications/js/model/sms-subscription-preferences-modal',
    'Wagento_SMSNotifications/js/model/sms-notifications'
], function ($, ko, Component, subscriptionPreferencesModal, smsNotifications) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Wagento_SMSNotifications/sms-subscription-preferences',
            groupedSmsTypes: {},
            selectedSmsTypes: [],
            modalTitle: null,
            tracks: {
                selectedSmsTypes: true
            }
        },
        modalTrigger: null,
        initObservable: function () {
            this._super();

            subscriptionPreferencesModal.open.subscribe(this.showModal);
            smsNotifications.selectedSmsTypes.subscribe(smsTypes => {
                if (smsTypes !== this.selectedSmsTypes) {
                    this.selectedSmsTypes = smsTypes;
                }
            }, this);

            const smsTypeCodes = this.groupedSmsTypes.map(groupedSmsType => groupedSmsType.smsTypes)
                .reduce(
                    (accumulator, currentValue) => [...accumulator, ...currentValue.map(smsType => smsType.code)],
                    []
                );

            this.selectAllSmsTypes = ko.pureComputed({
                read: () => this.selectedSmsTypes.length === smsTypeCodes.length,
                write: value => { this.selectedSmsTypes = value ? smsTypeCodes.slice(0) : []; },
                owner: this
            });

            if (this.selectedSmsTypes.length === 0) {
                this.selectAllSmsTypes(true);
            }

            return this;
        },
        initModal: function(element) {
            subscriptionPreferencesModal.createModal(element, this.modalTitle, this.hideModal.bind(this));
        },
        showModal: function () {
            subscriptionPreferencesModal.showModal();
        },
        hideModal: function () {
            smsNotifications.selectedSmsTypes(this.selectedSmsTypes);
        }
    });
});
