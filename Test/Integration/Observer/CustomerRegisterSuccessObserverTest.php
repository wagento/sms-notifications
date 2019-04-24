<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Test\Integration\Observer
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Test\Integration\Observer;

use Wagento\SMSNotifications\Api\ConfigInterface;
use Wagento\SMSNotifications\Api\SmsSubscriptionRepositoryInterface;
use Wagento\SMSNotifications\Model\SmsSender\WelcomeSender;
use Wagento\SMSNotifications\Model\SmsSubscription;
use Wagento\SMSNotifications\Observer\CustomerRegisterSuccessObserver;
use Wagento\SMSNotifications\Test\Integration\_stubs\Model\SmsSender;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ConfigInterface as EventObserverConfig;
use Magento\Framework\Event\ManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * customer_register_success Event Observer Test
 *
 * @package Wagento\SMSNotifications\Test\Integration\Observer
 * @author Joseph Leedy <joseph@wagento.com>
 */
class CustomerRegisterSuccessObserverTest extends TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;

    /**
     * @magentoAppArea frontend
     */
    public function testEventObserverIsConfigured(): void
    {
        /** @var \Magento\Framework\Event\ConfigInterface $observerConfig */
        $observerConfig = $this->objectManager->create(EventObserverConfig::class);
        $observers = $observerConfig->getObservers('customer_register_success');

        $this->assertArrayHasKey('sms_notifications_save_subscriptions', $observers);
        $this->assertSame(
            ltrim(CustomerRegisterSuccessObserver::class, '\\'),
            $observers['sms_notifications_save_subscriptions']['instance']
        );
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testObserverSavesSmsSubscriptionsAndSendsWelcomeSms(): void
    {
        $requestMock = $this->getMockForAbstractClass(RequestInterface::class, [], '', false, false, true, ['isPost']);
        $configMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $smsSenderMock = $this->getMockBuilder(SmsSender::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();
        $customerFactoryMock = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock->method('getParam')->with('sms_notifications')->willReturn([
            'subscribed' => 1,
            'sms_types' => 'order_placed,order_shipped'
        ]);
        $requestMock->method('isPost')->willReturn(true);
        $configMock->method('isEnabled')->willReturn(true);

        /** @var \Magento\Framework\Event\ManagerInterface $eventManager */
        $eventManager = $this->objectManager->create(ManagerInterface::class);
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->objectManager->create(Customer::class)->load(1);
        /** @var \Wagento\SMSNotifications\Api\SmsSubscriptionRepositoryInterface $smsSubscriptionRepository */
        $smsSubscriptionRepository = $this->objectManager->create(SmsSubscriptionRepositoryInterface::class);
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $this->objectManager->create(SearchCriteriaBuilder::class)
            ->addFilter('customer_id', '1')
            ->create();

        $smsSenderMock->expects($this->once())->method('send')->with($customer)->willReturn(true);
        $customerFactoryMock->expects($this->once())->method('create')->willReturn($customer);

        $this->objectManager->configure([
            get_class($requestMock) => ['shared' => true],
            get_class($configMock) => ['shared' => true],
            get_class($customerFactoryMock) => ['shared' => true],
            WelcomeSender::class => ['shared' => true],
            CustomerRegisterSuccessObserver::class => [
                'arguments' => [
                    'request' => ['instance' => get_class($requestMock)],
                    'config' => ['instance' => get_class($configMock)],
                    'customerFactory' => ['instance' => get_class($customerFactoryMock)],
                ]
            ]
        ]);
        $this->objectManager->addSharedInstance($requestMock, get_class($requestMock));
        $this->objectManager->addSharedInstance($configMock, get_class($configMock));
        $this->objectManager->addSharedInstance($customerFactoryMock, get_class($customerFactoryMock));
        $this->objectManager->addSharedInstance($smsSenderMock, WelcomeSender::class);

        $eventManager->dispatch('customer_register_success', ['customer' => $customer->getDataModel()]);

        $smsSubscriptionSearchResults = $smsSubscriptionRepository->getList($searchCriteria);
        $createdSmsSubscriptions = [];

        if ($smsSubscriptionSearchResults->getTotalCount() > 0) {
            $createdSmsSubscriptions = array_map(function (SmsSubscription $smsSubscription) {
                return $smsSubscription->getSmsType();
            }, $smsSubscriptionSearchResults->getItems());
        }

        $this->assertEquals(2, $smsSubscriptionSearchResults->getTotalCount());
        $this->assertContains('order_placed', $createdSmsSubscriptions);
        $this->assertContains('order_shipped', $createdSmsSubscriptions);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->objectManager = null;
    }
}
