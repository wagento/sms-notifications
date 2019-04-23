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

use Wagento\SMSNotifications\Model\SmsSender\CreditmemoSender;
use Wagento\SMSNotifications\Plugin\Sales\Api\CreditmemoRepositoryInterfacePlugin;
use Wagento\SMSNotifications\Test\Integration\_stubs\Model\SmsSender;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Interception\PluginList\PluginList;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Credit Memo Repository Interface Plugin Test
 *
 * @package Wagento\SMSNotifications\Test\Integration\Plugin\Sales\Api
 * @author Joseph Leedy <joseph@wagento.com>
 */
class CreditmemoRepositoryInterfacePluginTest extends TestCase
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
        $plugins = $pluginList->get(CreditmemoRepositoryInterface::class, []);

        $this->assertArrayHasKey('sms_notifications_send_refund_sms', $plugins);
        $this->assertSame(
            CreditmemoRepositoryInterfacePlugin::class,
            $plugins['sms_notifications_send_refund_sms']['instance']
        );
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture creditmemoDataFixtureProvider
     */
    public function testAfterSaveSendsOrderSms(): void
    {
        $smsSenderMock = $this->getMockBuilder(SmsSender::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();
        $creditmemoRepository = $this->objectManager->create(CreditmemoRepositoryInterface::class);
        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('increment_id', '100000001')->create();
        $searchResults = $creditmemoRepository->getList($searchCriteria);
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = current($searchResults->getItems());

        $smsSenderMock->expects($this->once())->method('send')->with($creditmemo)->willReturn(true);

        $this->objectManager->configure([CreditmemoSender::class => ['shared' => true]]);
        $this->objectManager->addSharedInstance($smsSenderMock, CreditmemoSender::class);

        $creditmemo->addComment('test');

        $creditmemoRepository->save($creditmemo);
    }

    public static function creditmemoDataFixtureProvider(): void
    {
        require __DIR__ . '/../../../_files/creditmemo.php';
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
