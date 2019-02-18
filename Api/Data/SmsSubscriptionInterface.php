<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Api\Data
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Api\Data;

/**
 * SMS Subscription Entity Interface
 *
 * @package LinkMobility\SMSNotifications\Api\Data
 * @author Joseph Leedy <joseph@wagento.com>
 */
interface SmsSubscriptionInterface
{
    const SMS_SUBSCRIPTION_ID = 'sms_subscription_id';
    const CUSTOMER_ID = 'customer_id';
    const SMS_TYPE = 'sms_type';

    /**
     * @param int $id
     * @return \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface
     */
    public function setId(int $id): SmsSubscriptionInterface;

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @param int $smsSubscriptionId
     * @return \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface
     */
    public function setSmsSubscriptionId(int $smsSubscriptionId): SmsSubscriptionInterface;

    /**
     * @return int|null
     */
    public function getSmsSubscriptionId(): ?int;

    /**
     * @param string $customerId
     * @return \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface
     */
    public function setCustomerId(string $customerId): SmsSubscriptionInterface;

    /**
     * @return string|null
     */
    public function getCustomerId(): ?string;

    /**
     * @param string $smsType
     * @return \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface
     */
    public function setSmsType(string $smsType): SmsSubscriptionInterface;

    /**
     * @return string|null
     */
    public function getSmsType(): ?string;
}
