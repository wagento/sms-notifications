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

define(['Magento_Ui/js/lib/core/storage/local'], function (storage) {
    "use strict";

    return function () {
        if (storage.get('sms-notification-subscription.isSubscribeChecked') !== undefined) {
            storage.remove('sms-notification-subscription.isSubscribeChecked')
        }
    };
});
