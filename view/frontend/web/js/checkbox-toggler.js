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
    'jquery'
],
function ($) {
    $.widget('wagento.checkboxToggler', {
        options: {
            checkboxSelector: '[data-role-toggleable-checkbox]'
        },
        checkboxElements: null,
        _create: function () {
            this.checkboxElements = $(this.options.checkboxSelector);

            this._registerEvents();
        },
        _registerEvents: function () {
            this.element.on('click', this.toggleCheckboxes.bind(this));
            this.checkboxElements.on('click', this.toggleSelectAll.bind(this));
        },
        toggleCheckboxes: function () {
            const self = this;

            this.checkboxElements.each(function () {
                $(this).prop('checked', self.element.prop('checked'));
            });
        },
        toggleSelectAll: function () {
            this.element.prop('checked', this.checkboxElements.filter(':checked').length === this.checkboxElements.length);
        }
    });

    return {
        'wagentoCheckboxToggler': $.wagento.checkboxToggler
    };
});
