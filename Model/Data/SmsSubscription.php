<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Model\Data
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Model\Data;

use LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionExtensionInterface;
use LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * SMS Subscription Entity
 *
 * @package LinkMobility\SMSNotifications\Model\Data
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class SmsSubscription extends AbstractExtensibleObject implements SmsSubscriptionInterface
{
    /**
     * @param int $id
     * @return \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface
     */
    public function setId(int $id): SmsSubscriptionInterface
    {
        return $this->setSmsSubscriptionId($id);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->getSmsSubscriptionId();
    }

    /**
     * @param int $smsSubscriptionId
     * @return \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface
     */
    public function setSmsSubscriptionId(int $smsSubscriptionId): SmsSubscriptionInterface
    {
        return $this->setData(self::SMS_SUBSCRIPTION_ID, $smsSubscriptionId);
    }

    /**
     * @return int|null
     */
    public function getSmsSubscriptionId(): ?int
    {
        return $this->_get(self::SMS_SUBSCRIPTION_ID);
    }

    /**
     * @param int $customerId
     * @return \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface
     */
    public function setCustomerId(int $customerId): SmsSubscriptionInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * @param string $smsType
     * @return \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface
     */
    public function setSmsType(string $smsType): SmsSubscriptionInterface
    {
        return $this->setData(self::SMS_TYPE, $smsType);
    }

    /**
     * @return string|null
     */
    public function getSmsType(): ?string
    {
        return $this->_get(self::SMS_TYPE);
    }

    /**
     * @param \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionExtensionInterface $smsSubscriptionExtension
     * @return \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface
     *
     * @phpcs:disable Generic.Files.LineLength.TooLong
     */
    public function setExtensionAttributes(SmsSubscriptionExtensionInterface $smsSubscriptionExtension): SmsSubscriptionInterface
    {
        return $this->_setExtensionAttributes($smsSubscriptionExtension);
    }

    /**
     * @return \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionExtensionInterface|null
     */
    public function getExtensionAttributes(): ?SmsSubscriptionExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }
}
