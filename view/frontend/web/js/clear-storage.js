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

define(['Magento_Ui/js/lib/core/storage/local'], function (storage) {
    "use strict";

    return function () {
        if (storage.get('sms-notification-subscription.isSubscribeChecked') !== undefined) {
            storage.remove('sms-notification-subscription.isSubscribeChecked');
        }

        if (storage.get('sms-notification-subscription.selectedSmsTypes') !== undefined) {
            storage.remove('sms-notification-subscription.selectedSmsTypes');
        }

        if (storage.get('sms-mobile-telephone') !== undefined) {
            storage.remove('sms-mobile-telephone');
        }
    };
});
