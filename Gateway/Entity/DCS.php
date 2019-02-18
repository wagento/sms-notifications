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

use MyCLabs\Enum\Enum;

/**
 * DCS Entity
 *
 * @package LinkMobility\SMSNotifications\Gateway\Entity
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class DCS extends Enum
{
    /**
     * GSM-7 default alphabet encoding
     */
    public const GSM = 'GSM';
    /**
     * 8-bit binary data
     */
    public const BINARY= 'BINARY';
    /**
     * UCS-2 encoding
     */
    public const UCS2 = 'UCS2';
    /**
     * Server side handling of encoding and segmenting
     */
    public const TEXT = 'TEXT';
}
