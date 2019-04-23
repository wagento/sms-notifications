<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Test\Integration\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Test\Integration\Model;

use Wagento\SMSNotifications\Test\Integration\_stubs\Model\SmsSender;
use Wagento\SMSNotifications\Test\Integration\SmsSenderTestCase;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Psr\Log\Test\TestLogger;

/**
 * SMS Sender Test
 *
 * @package Wagento\SMSNotifications\Test\Integration\Model
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @phpcs:disable Generic.Files.LineLength.TooLong
 */
class SmsSenderTest extends SmsSenderTestCase
{
    /**
     * @var \Wagento\SMSNotifications\Model\SmsSender
     */
    private $smsSender;

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetCustomerByIdReturnsCustomer(): void
    {
        $this->assertInstanceOf(CustomerInterface::class, $this->smsSender->getCustomerById(1));
    }

    public function testGetCustomerByIdWithInvalidCustomerIdReturnsNullAndLogsError(): void
    {
        $this->assertNull($this->smsSender->getCustomerById(100));
        $this->assertTrue(
            $this->objectManager->get(TestLogger::class)->hasCriticalThatPasses(function ($record) {
                return (string)$record['message'] ===
                    (string)__('Could not get customer by ID. Error: %1', 'No such entity with customerId = 100');
            })
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture smsMobileNumberFixtureProvider
     */
    public function testGetCustomerMobilePhoneNumberWithSubscribedCustomerReturnsPhoneNumber(): void
    {
        $customer = $this->objectManager->create(Customer::class)->load(1)->getDataModel();

        $this->assertSame('+15555551234', $this->smsSender->getCustomerMobilePhoneNumber($customer));
    }

    public function testGetCustomerMobilePhoneNumberWithUnsubscribedCustomerReturnsNull(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|\Magento\Customer\Api\Data\CustomerInterface $customerMock */
        $customerMock = $this->getMockBuilder(CustomerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCustomAttribute'])
            ->getMockForAbstractClass();

        $customerMock->expects($this->at(0))
            ->method('getCustomAttribute')
            ->with('sms_mobile_phone_prefix')
            ->willReturn(null);
        $customerMock->expects($this->at(1))
            ->method('getCustomAttribute')
            ->with('sms_mobile_phone_number')
            ->willReturn(null);

        $this->assertNull($this->smsSender->getCustomerMobilePhoneNumber($customerMock));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture smsSubscriptionFixtureProvider
     */
    public function testGetCustomerSmsSubscriptionsReturnsSubscribedSmsTypes(): void
    {
        $this->assertContains('order_placed', $this->smsSender->getCustomerSmsSubscriptions(1));
    }

    protected function setUp()
    {
        parent::setUp();

        $this->smsSender = $this->objectManager->create(
            SmsSender::class,
            [
                'logger' => $this->objectManager->get(TestLogger::class)
            ]
        );
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->smsSender = null;
    }
}
