<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Test\Integration
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Test\Integration;

use Linkmobility\Notifications\Api\ConfigInterface;
use Linkmobility\Notifications\Model\MessageService;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * SMS Sender Test Case
 *
 * @package Linkmobility\Notifications\Test\Integration
 * @author Joseph Leedy <joseph@wagento.com>
 */
class SmsSenderTestCase extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface|\Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public static function smsMobileNumberFixtureProvider(): void
    {
        $customerRepository = ObjectManager::getInstance()->create(CustomerRepositoryInterface::class);
        $customer = $customerRepository->getById(1);

        $customer->setCustomAttribute('sms_mobile_phone_prefix', 'US_1');
        $customer->setCustomAttribute('sms_mobile_phone_number', '555-555-1234');

        $customerRepository->save($customer);
    }

    public static function smsSubscriptionFixtureProvider()
    {
        return require __DIR__ . '/_files/create_sms_subscription.php';
    }

    public static function smsSubscriptionsFixtureProvider(int $count = -1)
    {
        return require __DIR__ . '/_files/create_sms_subscriptions_from_source.php';
    }

    protected function getConfigMock(): MockObject
    {
        /** @var \Linkmobility\Notifications\Api\ConfigInterface|\PHPUnit\Framework\MockObject\MockObject $configMock */
        $configMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $configMock->method('isEnabled')->willReturn(true);

        return $configMock;
    }

    protected function getMessageServiceMock(): MockObject
    {
        $messageServiceMock = $this->getMockBuilder(MessageService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $messageServiceMock->method('setOrder')->willReturnSelf();
        $messageServiceMock->method('setShipment')->willReturnSelf();
        $messageServiceMock->method('sendMessage')->willReturn(true);

        return $messageServiceMock;
    }
}
