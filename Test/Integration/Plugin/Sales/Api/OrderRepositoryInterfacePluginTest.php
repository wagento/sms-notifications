<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Test\Integration\Plugin\Sales\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Test\Integration\Plugin\Sales\Api;

use Wagento\SMSNotifications\Model\SmsSender\OrderSender;
use Wagento\SMSNotifications\Plugin\Sales\Api\OrderRepositoryInterfacePlugin;
use Wagento\SMSNotifications\Test\Integration\_stubs\Model\SmsSender;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Interception\PluginList\PluginList;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Order Repository Interface Plugin Test
 *
 * @package Wagento\SMSNotifications\Test\Integration\Plugin\Sales\Api
 * @author Joseph Leedy <joseph@wagento.com>
 */
class OrderRepositoryInterfacePluginTest extends TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;

    /**
     * @magentoAppArea frontend
     */
    public function testPluginIsConfigured(): void
    {
        $pluginList = $this->objectManager->create(PluginList::class);
        $plugins = $pluginList->get(OrderRepositoryInterface::class, []);

        $this->assertArrayHasKey('sms_notifications_send_order_sms', $plugins);
        $this->assertSame(
            OrderRepositoryInterfacePlugin::class,
            $plugins['sms_notifications_send_order_sms']['instance']
        );
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture orderDataFixtureProvider
     */
    public function testAfterSaveSendsOrderSms(): void
    {
        $smsSenderMock = $this->getMockBuilder(SmsSender::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();
        $orderRepository = $this->objectManager->create(OrderRepositoryInterface::class);
        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('increment_id', '100000001')->create();
        $searchResults = $orderRepository->getList($searchCriteria);
        $order = current($searchResults->getItems());

        $smsSenderMock->expects($this->once())->method('send')->with($order)->willReturn(true);

        $this->objectManager->configure([OrderSender::class => ['shared' => true]]);
        $this->objectManager->addSharedInstance($smsSenderMock, OrderSender::class);

        $order->setCustomerIsGuest(true);

        $orderRepository->save($order);
    }

    public static function orderDataFixtureProvider(): void
    {
        require __DIR__ . '/../../../_files/order.php';
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
}
