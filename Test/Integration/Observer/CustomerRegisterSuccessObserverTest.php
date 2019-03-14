<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Test\Integration\Observer
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Test\Integration\Observer;

use LinkMobility\SMSNotifications\Api\ConfigInterface;
use LinkMobility\SMSNotifications\Api\SmsSubscriptionRepositoryInterface;
use LinkMobility\SMSNotifications\Model\SmsSubscription;
use LinkMobility\SMSNotifications\Observer\CustomerRegisterSuccessObserver;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ConfigInterface as EventObserverConfig;
use Magento\Framework\Event\ManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * customer_register_success Event Observer Test
 *
 * @package LinkMobility\SMSNotifications\Test\Integration\Observer
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
    public function testObserverSavesSmsSubscriptions(): void
    {
        $requestMock = $this->getMockForAbstractClass(RequestInterface::class, [], '', false, false, true, ['isPost']);
        $configMock = $this->getMockBuilder(ConfigInterface::class)
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
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $this->objectManager->create(CustomerRepositoryInterface::class)->getById(1);
        /** @var \LinkMobility\SMSNotifications\Api\SmsSubscriptionRepositoryInterface $smsSubscriptionRepository */
        $smsSubscriptionRepository = $this->objectManager->create(SmsSubscriptionRepositoryInterface::class);
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $this->objectManager->create(SearchCriteriaBuilder::class)
            ->addFilter('customer_id', '1')
            ->create();

        $this->objectManager->configure([
            get_class($requestMock) => ['shared' => true],
            get_class($configMock) => ['shared' => true],
            CustomerRegisterSuccessObserver::class => [
                'arguments' => [
                    'request' => ['instance' => get_class($requestMock)],
                    'config' => ['instance' => get_class($configMock)],
                ]
            ]
        ]);
        $this->objectManager->addSharedInstance($requestMock, get_class($requestMock));
        $this->objectManager->addSharedInstance($configMock, get_class($configMock));

        $eventManager->dispatch('customer_register_success', ['customer' => $customer]);

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
}
