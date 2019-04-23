<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Gateway\Entity
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Gateway\Entity;

/**
 * Result Entity Interface
 *
 * @package Wagento\SMSNotifications\Gateway\Entity
 * @author Joseph Leedy <joseph@wagento.com>
 */
interface ResultInterface
{
    public function getType(): string;

    public function getCode(): int;

    public function getMessage(): string;

    public function toArray(): array;
}
