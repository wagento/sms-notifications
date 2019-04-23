<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Gateway\Factory
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Gateway\Factory;

use Wagento\SMSNotifications\Gateway\Entity\Message;
use Wagento\SMSNotifications\Gateway\Entity\MessageInterface;

/**
 * Message Entity Factory
 *
 * @package Wagento\SMSNotifications\Gateway\Factory
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class MessageFactory
{
    public function create(
        string $source = null,
        string $destination = null,
        string $userData = null,
        string $platformId = null,
        string $platformPartnerId = null
    ): MessageInterface {
        return new Message($source, $destination, $userData, $platformId, $platformPartnerId);
    }
}
