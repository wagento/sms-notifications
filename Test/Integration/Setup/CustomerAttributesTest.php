<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Test\Integration\Setup
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Test\Integration\Setup;

use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Customer Attribute Creation Test
 *
 * @package Wagento\SMSNotifications\Test\Integration\Setup
 * @author Joseph Leedy <joseph@wagento.com>
 */
class CustomerAttributesTest extends TestCase
{
    /**
     * @dataProvider mobilePhoneAttributeProvider
     */
    public function testMobilePhoneAttributeIsCreated(string $attributeName): void
    {
        /** @var \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository */
        $attributeRepository = Bootstrap::getObjectManager()->create(AttributeRepositoryInterface::class);

        try {
            $attribute = $attributeRepository->get('customer', $attributeName);
        } catch (NoSuchEntityException $e) {
            $this->fail($e->getMessage());
        }

        $this->assertInstanceOf(AttributeInterface::class, $attribute);
    }

    public static function mobilePhoneAttributeProvider(): array
    {
        return [
            [
                'sms_mobile_phone_prefix'
            ],
            [
                'sms_mobile_phone_number'
            ]
        ];
    }
}
