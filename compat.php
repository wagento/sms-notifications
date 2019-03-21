<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Controller\SmsNotifications
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;

if (!interface_exists(CsrfAwareActionInterface::class)) {
    class_alias(\LinkMobility\SMSNotifications\Compat\CsrfAwareActionInterface::class, CsrfAwareActionInterface::class);
    class_alias(\LinkMobility\SMSNotifications\Compat\InvalidRequestException::class, InvalidRequestException::class);
}
