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
    const SMS_TYPE = 'sms_type';
    const SMS_TYPE_ID = 'sms_type_id';

    /**
     * @param int $id
     * @return \Linkmobility\Notifications\Api\Data\SmsSubscriptionInterface
     */
    public function setId(int $id): SmsSubscriptionInterface;

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @param int $smsSubscriptionId
     * @return \Linkmobility\Notifications\Api\Data\SmsSubscriptionInterface
     */
    public function setSmsSubscriptionId(int $smsSubscriptionId): SmsSubscriptionInterface;

    /**
     * @return int|null
     */
    public function getSmsSubscriptionId(): ?int;

    /**
     * @param string $customerId
     * @return \Linkmobility\Notifications\Api\Data\SmsSubscriptionInterface
     */
    public function setCustomerId(string $customerId): SmsSubscriptionInterface;

    /**
     * @return string|null
     */
    public function getCustomerId(): ?string;

    /**
     * @param string $smsType
     * @return \Linkmobility\Notifications\Api\Data\SmsSubscriptionInterface
     */
    public function setSmsType(string $smsType): SmsSubscriptionInterface;

    /**
     * @return string|null
     */
    public function getSmsType(): ?string;

    /**
     * @param int $smsTypeId
     * @return \Linkmobility\Notifications\Api\Data\SmsSubscriptionInterface
     */
    public function setSmsTypeId(int $smsTypeId): SmsSubscriptionInterface;

    /**
     * @return int|null
     */
    public function getSmsTypeId(): ?int;
}
