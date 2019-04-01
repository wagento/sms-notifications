/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
var config = {
    config: {
        mixins: {
            'Magento_Ui/js/grid/massactions': {
                'Wagento_LinkMobilitySMSNotifications/js/grid/massactions-mixin': true
            }
        }
    }
};