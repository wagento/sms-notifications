<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Test\Integration\Observer
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Test\Integration\Observer;

use Linkmobility\Notifications\Api\ConfigInterface;
use Linkmobility\Notifications\Api\SmsSubscriptionRepositoryInterface;
use Linkmobility\Notifications\Model\SmsSubscription;
use Linkmobility\Notifications\Observer\CustomerRegisterSuccessObserver;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;

/**
 * customer_register_success Event Observer Test
 *
 * @package Linkmobility\Notifications\Test\Integration\Observer
 * @author Joseph Leedy <joseph@wagento.com>
 */
class CustomerRegisterSuccessObserverTest extends TestCase
{
    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testObserverSavesSmsSubscriptions(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

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
        $eventManager = $objectManager->create(ManagerInterface::class);
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $objectManager->create(CustomerRepositoryInterface::class)->getById(1);
        /** @var \Linkmobility\Notifications\Api\SmsSubscriptionRepositoryInterface $smsSubscriptionRepository */
        $smsSubscriptionRepository = $objectManager->create(SmsSubscriptionRepositoryInterface::class);
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $objectManager->create(SearchCriteriaBuilder::class)->addFilter('customer_id', '1')->create();

        $objectManager->configure([
            get_class($requestMock) => ['shared' => true],
            get_class($configMock) => ['shared' => true],
            CustomerRegisterSuccessObserver::class => [
                'arguments' => [
                    'logger' => ['instance' => TestLogger::class],
                    'request' => ['instance' => get_class($requestMock)],
                    'config' => ['instance' => get_class($configMock)],
                ]
            ]
        ]);
        $objectManager->addSharedInstance($requestMock, get_class($requestMock));
        $objectManager->addSharedInstance($configMock, get_class($configMock));

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
}
