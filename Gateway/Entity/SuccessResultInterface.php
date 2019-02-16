<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Gateway\Entity
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Gateway\Entity;

/**
 * Success Result Entity Interface
 *
 * @package LinkMobility\SMSNotifications\Gateway\Entity
 * @author Joseph Leedy <joseph@wagento.com>
 */
interface SuccessResultInterface extends ResultInterface
{
    public function setMessageId(string $messageId): void;

    public function getMessageId(): string;

    public function setResultCode(int $resultCode): void;

    public function getResultCode(): int;

    public function setDescription(string $description): void;

    public function getDescription(): string;

    public function setSmsCount(int $smsCount): void;

    public function getSmsCount(): int;
}
