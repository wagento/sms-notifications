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
 * Telephone Prefix Entity Interface
 *
 * @package Linkmobility\Notifications\Api\Data
 * @author Joseph Leedy <joseph@wagento.com>
 */
interface TelephonePrefixInterface
{
    public function setCountryCode(string $countryCode): TelephonePrefixInterface;

    public function getCountryCode(): string;

    public function setPrefix(int $prefix): TelephonePrefixInterface;

    public function getPrefix(): int;
}
