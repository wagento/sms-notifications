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
    public const COUNTRY_CODE = 'country_code';
    public const COUNTRY_NAME = 'country_name';
    public const PREFIX = 'prefix';

    public function setCountryCode(string $countryCode): TelephonePrefixInterface;

    public function getCountryCode(): string;

    public function setCountryName(string $countryName): TelephonePrefixInterface;

    public function getCountryName(): string;

    public function setPrefix(int $prefix): TelephonePrefixInterface;

    public function getPrefix(): int;
}
