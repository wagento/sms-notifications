<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Gateway\Entity
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace Linkmobility\Notifications\Gateway\Entity;

/**
 * Result Entity Interface
 *
 * @package Linkmobility\Notifications\Gateway\Entity
 * @author Joseph Leedy <joseph@wagento.com>
 */
interface ResultInterface
{
    public function getType(): string;

    public function getCode(): int;

    public function getMessage(): string;

    public function toArray(): array;
}
