<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Controller\SmsNotifications
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

/**
 * NOTICE: This is named with a ".inc" extension instead of ".php" to avoid a
 * bug in the Magento 2.3 class compiler that causes the `class_alias` function
 * to be parsed as a class, causing a "Class does not exist" exception.
 */

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;

if (!interface_exists(CsrfAwareActionInterface::class)) {
    class_alias(\Wagento\SMSNotifications\Compat\CsrfAwareActionInterface::class, CsrfAwareActionInterface::class);
    class_alias(\Wagento\SMSNotifications\Compat\InvalidRequestException::class, InvalidRequestException::class);
}
