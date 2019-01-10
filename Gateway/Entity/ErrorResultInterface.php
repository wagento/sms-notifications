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
 * Error Result Entity Interface
 *
 * @package Linkmobility\Notifications\Gateway\Entity
 */
interface ErrorResultInterface extends ResultInterface
{
    public function setStatus(int $status): void;

    public function getStatus(): int;

    public function setDescription(string $description): void;

    public function getDescription(): string;

    public function setTranslatedDescription(?string $translatedDescription): void;

    public function getTranslatedDescription(): ?string;
}
