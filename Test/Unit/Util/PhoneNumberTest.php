<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Test\Unit\Util
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Test\Unit\Util;

use Linkmobility\Notifications\Util\PhoneNumber;
use PHPUnit\Framework\TestCase;

/**
 * Phone Number Utility Test
 *
 * @package Linkmobility\Notifications\Test\Unit\Util
 * @author Joseph Leedy <joseph@wagento.com>
 */
class PhoneNumberTest extends TestCase
{
    /**
     * @dataProvider prefixDataProvider
     */
    public function testGetPrefixReturnsPrefix($phoneNumber, $expected)
    {
        $this->assertSame($expected, PhoneNumber::getPrefix($phoneNumber));
    }

    /**
     * @dataProvider phoneNumberDataProvider
     */
    public function testRemovePrefixReturnsNumberOnly($phoneNumber, $expected)
    {
        $this->assertSame($expected, PhoneNumber::removePrefix($phoneNumber));
    }

    public static function prefixDataProvider()
    {
        return [
            'us_phone' => [
                '+15555551234',
                '1'
            ],
            'eg_phone' => [
                '+201012345678',
                '20'
            ],
            'uk_phone' => [
                '+442072343456',
                '44'
            ],
            'bo_phone' => [
                '+59171234567',
                '591'
            ]
        ];
    }

    public static function phoneNumberDataProvider()
    {
        return [
            'us_phone' => [
                '+15555551234',
                '5555551234'
            ],
            'eg_phone' => [
                '+201012345678',
                '1012345678'
            ],
            'uk_phone' => [
                '+442072343456',
                '2072343456'
            ],
            'bo_phone' => [
                '+59171234567',
                '71234567'
            ]
        ];
    }
}
