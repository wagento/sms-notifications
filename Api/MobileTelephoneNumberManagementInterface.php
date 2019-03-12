<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Api;

use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Mobile Telephone Number Management Service Interface
 *
 * @package LinkMobility\SMSNotifications\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 */
interface MobileTelephoneNumberManagementInterface
{
    public function updateNumber(
        string $newMobileTelephonePrefix,
        string $newMobileTelephoneNumber,
        CustomerInterface $customer
    ): ?bool;
}
