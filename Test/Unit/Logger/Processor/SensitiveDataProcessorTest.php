<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Test\Unit\Logger\Processor
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Test\Unit\Logger\Processor;

use Wagento\SMSNotifications\Logger\Processor\SensitiveDataProcessor;
use PHPUnit\Framework\TestCase;

/**
 * Sensitive Log Data Processor Test
 *
 * @package Wagento\SMSNotifications\Test\Unit\Logger\Processor
 * @author Joseph Leedy <joseph@wagento.com>
 */
class SensitiveDataProcessorTest extends TestCase
{
    public function testProcessorIsCallable()
    {
        $sensitiveDataProcessor = new SensitiveDataProcessor();

        $this->assertTrue(is_callable($sensitiveDataProcessor));
    }

    public function testRedactsSensitiveDataInRecordContext(): void
    {
        $testObject = new \stdClass();
        $testObject->field0 = 'foo';
        $testObject->field1 = 'bar';
        $testObject->field2 = 'sensitive';
        $testObject->field3 = new \stdClass();
        $testObject->field3->subfield0 = 'foo';

        $resultObject = clone $testObject;
        $resultObject->field2 = '*********';

        $record = [
            'context' => [
                'password' => 'foobar123',
                'fruits'   => [
                    'apple',
                    'orange',
                    'cherry'
                ],
                'object'   => $testObject
            ],
            'extra'   => [],
        ];
        $expected = array_replace_recursive($record, [
            'context' => [
                'password' => '*********',
                'object'   => $resultObject
            ]
        ]);
        $sensitiveDataProcessor = new SensitiveDataProcessor(['field2']);
        $actual = call_user_func($sensitiveDataProcessor, $record);

        $this->assertEquals($expected, $actual);
    }
}
