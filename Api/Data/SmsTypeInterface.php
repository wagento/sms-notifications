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
 * SMS Type Entity Interface
 *
 * @package Linkmobility\Notifications\Api\Data
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 */
interface SmsTypeInterface
{
    const SMS_TYPE_ID = 'sms_type_id';
    const NAME = 'name';
    const IS_ACTIVE = 'is_active';
    const DESCRIPTION = 'description';

    public function setId(int $smsTypeId): SmsTypeInterface;
    public function getId(): ?int;

    public function setSmsTypeId(int $smsTypeId): SmsTypeInterface;
    public function getSmsTypeId(): ?int;

    public function setName(string $name): SmsTypeInterface;
    public function getName(): ?string;

    public function setIsActive(bool $isActive): SmsTypeInterface;
    public function getIsActive(): bool;

    public function setDescription(string $description): SmsTypeInterface;
    public function getDescription(): ?string;
}
