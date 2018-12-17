<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Api\Data
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace Linkmobility\Notifications\Api\Data;

/**
 * SMS Subscription Entity Interface
 *
 * @package Linkmobility\Notifications\Api\Data
 * @author Joseph Leedy <joseph@wagento.com>
 */
interface SmsSubscriptionInterface
{
    const SMS_SUBSCRIPTION_ID = 'sms_subscription_id';
    const CUSTOMER_ID = 'customer_id';
    const SMS_TYPE_ID = 'sms_type_id';
    const IS_ACTIVE = 'is_active';

    public function setId(int $id): SmsSubscriptionInterface;
    public function getId(): ?int;

    public function setSmsSubscriptionId(int $smsSubscriptionId): SmsSubscriptionInterface;
    public function getSmsSubscriptionId(): ?int;

    public function setCustomerId(string $customerId): SmsSubscriptionInterface;
    public function getCustomerId(): ?string;

    public function setSmsTypeId(int $smsTypeId): SmsSubscriptionInterface;
    public function getSmsTypeId(): ?int;

    public function setIsActive(bool $isActive): SmsSubscriptionInterface;
    public function getIsActive(): bool;
}
