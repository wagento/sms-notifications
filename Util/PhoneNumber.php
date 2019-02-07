<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Util
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Util;

/**
 * Phone Number Utility
 *
 * @package Linkmobility\Notifications\Util
 * @author Joseph Leedy <joseph@wagento.com>
 */
class PhoneNumber
{
    public static function getPrefix($phoneNumber): string
    {
        if ($phoneNumber{0} === '+') {
            $phoneNumber = substr($phoneNumber, 1);
        }

        $phoneNumber = preg_replace('/\D+/', '', $phoneNumber);
        $singleDigitPrefixes = [1, 7];
        $doubleDigitPrefixes = [20, 27, 30, 31, 32, 33, 34, 36, 39, 40, 41, 43, 44, 45, 46, 47, 48, 49, 51, 52, 53, 54,
            55, 56, 57, 58, 60, 61, 62, 63, 64, 65, 66, 77, 81, 82, 84, 86, 90, 91, 92, 93, 94, 95, 98];

        if (in_array($phoneNumber{0}, $singleDigitPrefixes, false)) {
            return $phoneNumber{0};
        }

        if (in_array(substr($phoneNumber, 0, 2), $doubleDigitPrefixes, false)) {
            return substr($phoneNumber, 0, 2);
        }

        return substr($phoneNumber, 0, 3);
    }

    public static function removePrefix($phoneNumber): string
    {
        $prefix = self::getPrefix($phoneNumber);

        return substr($phoneNumber, strlen($prefix) + 1);
    }
}
