<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\LinkMobilitySMSNotifications\Api\Data
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\LinkMobilitySMSNotifications\Api\Data;

use \Magento\Framework\Api\ExtensibleDataInterface;

/**
 * SMS Subscription Entity Interface
 *
 * @package Wagento\LinkMobilitySMSNotifications\Api\Data
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 */
interface SmsSubscriptionInterface extends ExtensibleDataInterface
{
    const SMS_SUBSCRIPTION_ID = 'sms_subscription_id';
    const CUSTOMER_ID = 'customer_id';
    const SMS_TYPE = 'sms_type';

    /**
     * @param int $id
     * @return \Wagento\LinkMobilitySMSNotifications\Api\Data\SmsSubscriptionInterface
     */
    public function setId(int $id): SmsSubscriptionInterface;

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @param int $smsSubscriptionId
     * @return \Wagento\LinkMobilitySMSNotifications\Api\Data\SmsSubscriptionInterface
     */
    public function setSmsSubscriptionId(int $smsSubscriptionId): SmsSubscriptionInterface;

    /**
     * @return int|null
     */
    public function getSmsSubscriptionId(): ?int;

    /**
     * @param int $customerId
     * @return \Wagento\LinkMobilitySMSNotifications\Api\Data\SmsSubscriptionInterface
     */
    public function setCustomerId(int $customerId): SmsSubscriptionInterface;

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int;

    /**
     * @param string $smsType
     * @return \Wagento\LinkMobilitySMSNotifications\Api\Data\SmsSubscriptionInterface
     */
    public function setSmsType(string $smsType): SmsSubscriptionInterface;

    /**
     * @return string|null
     */
    public function getSmsType(): ?string;

    /**
     * @param \Wagento\LinkMobilitySMSNotifications\Api\Data\SmsSubscriptionExtensionInterface $smsSubscriptionExtension
     * @return \Wagento\LinkMobilitySMSNotifications\Api\Data\SmsSubscriptionInterface
     *
     * @phpcs:disable Generic.Files.LineLength.TooLong
     */
    public function setExtensionAttributes(SmsSubscriptionExtensionInterface $smsSubscriptionExtension): SmsSubscriptionInterface;

    /**
     * @return \Wagento\LinkMobilitySMSNotifications\Api\Data\SmsSubscriptionExtensionInterface|null
     */
    public function getExtensionAttributes(): ?SmsSubscriptionExtensionInterface;
}
