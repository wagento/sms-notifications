<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Gateway\Factory
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Gateway\Factory;

use LinkMobility\SMSNotifications\Gateway\Entity\Message;
use LinkMobility\SMSNotifications\Gateway\Entity\MessageInterface;

/**
 * Message Entity Factory
 *
 * @package LinkMobility\SMSNotifications\Gateway\Factory
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
