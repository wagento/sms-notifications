<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Model\Data
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace Linkmobility\Notifications\Model\Data;

use Linkmobility\Notifications\Api\Data\SmsSubscriptionInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * SMS Subscription Entity
 *
 * @package Linkmobility\Notifications\Model\Data
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class SmsSubscription extends AbstractSimpleObject implements SmsSubscriptionInterface
{
    public function setId(int $id): SmsSubscriptionInterface
    {
        return $this->setSmsSubscriptionId($id);
    }

    public function getId(): ?int
    {
        return $this->getSmsSubscriptionId();
    }

    public function setSmsSubscriptionId(int $smsSubscriptionId): SmsSubscriptionInterface
    {
        return $this->setData(self::SMS_SUBSCRIPTION_ID, $smsSubscriptionId);
    }

    public function getSmsSubscriptionId(): ?int
    {
        return $this->_get(self::SMS_SUBSCRIPTION_ID);
    }

    public function setCustomerId(string $customerId): SmsSubscriptionInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function getCustomerId(): ?string
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    public function setSmsTypeId(int $smsTypeId): SmsSubscriptionInterface
    {
        return $this->setData(self::SMS_TYPE_ID, $smsTypeId);
    }

    public function getSmsTypeId(): ?int
    {
        return $this->_get(self::SMS_TYPE_ID);
    }

    public function setIsActive(bool $isActive): SmsSubscriptionInterface
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    public function getIsActive(): bool
    {
        return (bool)$this->_get(self::IS_ACTIVE);
    }
}
