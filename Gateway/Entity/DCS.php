<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
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

use MyCLabs\Enum\Enum;

/**
 * DCS Entity
 *
 * @package Wagento\SMSNotifications\Gateway\Entity
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @phpcs:disable Magento2.PHP.FinalImplementation.FoundFinal -- This ENUM object is not meant to be extended.
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
    public const BINARY = 'BINARY';
    /**
     * UCS-2 encoding
     */
    public const UCS2 = 'UCS2';
    /**
     * Server side handling of encoding and segmenting
     */
    public const TEXT = 'TEXT';
}
