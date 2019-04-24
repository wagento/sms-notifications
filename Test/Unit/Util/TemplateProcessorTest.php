<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Test\Unit\Util
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Test\Unit\Util;

use Wagento\SMSNotifications\Util\TemplateProcessor;
use PHPUnit\Framework\TestCase;
use Zend\Filter\Word\SeparatorToCamelCase;

/**
 * Template Processor Test
 *
 * @package Wagento\SMSNotifications\Test\Unit\Util
 * @author Joseph Leedy <joseph@wagento.com>
 */
class TemplateProcessorTest extends TestCase
{
    /**
     * @var \Wagento\SMSNotifications\Util\TemplateProcessorInterface
     */
    private $templateProcessor;

    public function testProcessString(): void
    {
        $message = 'The quick brown {{prey}} jumps over the lazy {{predator}}.';
        $data = [
            'prey' => 'fox',
            'predator' => 'dog'
        ];
        $expected = 'The quick brown fox jumps over the lazy dog.';
        $actual = $this->templateProcessor->process($message, $data);

        $this->assertEquals($expected, $actual);
    }

    public function testProcessArray(): void
    {
        $message = 'There are {{count}} fruits available: {{fruits}}.';
        $data = [
            'count' => 3,
            'fruits' => [
                'apple',
                'orange',
                'pear'
            ]
        ];
        $expected = 'There are 3 fruits available: apple, orange, pear.';
        $actual = $this->templateProcessor->process($message, $data);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider processObjectDataProvider
     */
    public function testProcessObject($message, $data, $expected): void
    {
        $actual = $this->templateProcessor->process($message, $data);

        $this->assertEquals($expected, $actual);
    }

    public static function processObjectDataProvider(): array
    {
        $orderWithProperties = new class() {
            public $id = '000123ABC';
            public $total = 123.45;
        };
        $orderWithGetters = new class() {
            private $id = '000123ABC';
            private $total = 123.45;

            public function getId(): string
            {
                return $this->id;
            }

            public function getTotal(): float
            {
                return $this->total;
            }
        };
        $customerWithProperties = new class() {
            public $id = 1;
            public $name = 'John Smith';
            public $email = 'jsmith@example.com';
        };

        return [
            'single variable with object property' => [
                'Your order ID is {{order.id}}.',
                [
                    'order' => $orderWithProperties
                ],
                'Your order ID is 000123ABC.'
            ],
            'multiple variables with object property and same object' => [
                'Your order ID is {{order.id}}. Order total: {{order.total}}',
                [
                    'order' => $orderWithProperties
                ],
                'Your order ID is 000123ABC. Order total: 123.45'
            ],
            'multiple variables with object property and different objects' => [
                'Hello {{customer.name}}! Your order ID is {{order.id}}.',
                [
                    'order' => $orderWithProperties,
                    'customer' => $customerWithProperties
                ],
                'Hello John Smith! Your order ID is 000123ABC.'
            ],
            'single variable with object getter' => [
                'Your order ID is {{order.id}}.',
                [
                    'order' => $orderWithGetters
                ],
                'Your order ID is 000123ABC.'
            ],
        ];
    }

    protected function setUp()
    {
        parent::setUp();

        $this->templateProcessor = new TemplateProcessor(new SeparatorToCamelCase());
    }
}
