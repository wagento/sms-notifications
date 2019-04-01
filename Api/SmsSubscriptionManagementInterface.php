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
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Api;

/**
 * SMS Subscription Management Service Interface
 *
 * @package LinkMobility\SMSNotifications\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 */
interface SmsSubscriptionManagementInterface
{
    public function createSubscription(string $smsType, int $customerId): bool;

    /**
     * @param string[] $smsTypes
     * @param string[] $messages
     */
    public function createSubscriptions(array $smsTypes, int $customerId, array $messages = []): int;

    public function removeSubscription(int $subscriptionId, int $customerId): bool;

    /**
     * @param \LinkMobility\SMSNotifications\Model\SmsSubscription[] $subscribedSmsTypes
     * @param string[] $selectedSmsTypes
     * @param string[] $messages
     */
    public function removeSubscriptions(
        array &$subscribedSmsTypes,
        array $selectedSmsTypes,
        int $customerId,
        array $messages = []
    ): int;
}
