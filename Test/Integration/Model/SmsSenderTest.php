<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Test\Integration\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Test\Integration\Model;

use Linkmobility\Notifications\Test\Integration\_stubs\Model\SmsSender;
use Linkmobility\Notifications\Test\Integration\SmsSenderTestCase;
use Psr\Log\Test\TestLogger;

/**
 * SMS Sender Test
 *
 * @package Linkmobility\Notifications\Test\Integration\Model
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @phpcs:disable Generic.Files.LineLength.TooLong
 */
class SmsSenderTest extends SmsSenderTestCase
{
    /**
     * @var \Linkmobility\Notifications\Model\SmsSender
     */
    private $smsSender;

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture smsMobileNumberFixtureProvider
     */
    public function testGetCustomerMobilePhoneNumberReturnsPhoneNumber(): void
    {
        $this->assertSame('+15555551234', $this->smsSender->getCustomerMobilePhoneNumber(1));
    }

    public function testGetCustomerMobilePhoneNumberWithInvalidCustomerIdReturnsNullAndLogsError(): void
    {
        $this->assertNull($this->smsSender->getCustomerMobilePhoneNumber(100));
        $this->assertTrue(
            $this->objectManager->get(TestLogger::class)->hasCriticalThatPasses(function ($record) {
                return (string)$record['message'] ===
                    (string)__('Could not get mobile telephone number for customer. Error: %1', 'No such entity with customerId = 100');
            })
        );
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
}
