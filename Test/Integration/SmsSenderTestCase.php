<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Test\Integration
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Test\Integration;

use Wagento\SMSNotifications\Api\ConfigInterface;
use Wagento\SMSNotifications\Api\Data\SmsSubscriptionInterface;
use Wagento\SMSNotifications\Model\MessageService;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * SMS Sender Test Case
 *
 * @package Wagento\SMSNotifications\Test\Integration
 * @author Joseph Leedy <joseph@wagento.com>
 */
class SmsSenderTestCase extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface|\Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

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

    public static function smsSubscriptionFixtureProvider(): SmsSubscriptionInterface
    {
        $smsSubscription = require __DIR__ . '/_files/create_sms_subscription.php';

        return $smsSubscription->getDataModel();
    }

    public static function smsSubscriptionsFixtureProvider(int $count = -1): array
    {
        return require __DIR__ . '/_files/create_sms_subscriptions_from_source.php';
    }

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->objectManager = null;
    }

    /**
     * @phpcs:disable Generic.Files.LineLength.TooLong
     */
    protected function getConfigMock(): MockObject
    {
        /** @var \Wagento\SMSNotifications\Api\ConfigInterface|\PHPUnit\Framework\MockObject\MockObject $configMock */
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
