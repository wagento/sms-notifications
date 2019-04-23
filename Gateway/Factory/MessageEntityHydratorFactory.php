<?php
/**
 * LINK Mobility SMS Notifications
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

use Wagento\SMSNotifications\Gateway\Entity\DCS;
use Wagento\SMSNotifications\Gateway\Entity\TON;
use Wagento\SMSNotifications\Gateway\Hydrator\MessageEntity as MessageEntityHydrator;
use Wagento\SMSNotifications\Gateway\Hydrator\Strategy\Enum as EnumStrategy;

/**
 * Message Entity Hydrator Factory
 *
 * @package Wagento\SMSNotifications\Gateway\Factory
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class MessageEntityHydratorFactory
{
    public function create(): MessageEntityHydrator
    {
        $messageHydrator = new MessageEntityHydrator();

        $messageHydrator->addStrategy('sourceTON', new EnumStrategy(TON::class));
        $messageHydrator->addStrategy('destinationTON', new EnumStrategy(TON::class));
        $messageHydrator->addStrategy('dcs', new EnumStrategy(DCS::class));

        return $messageHydrator;
    }
}
