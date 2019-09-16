<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Test\Integration\Plugin\Customer\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Test\Integration\Plugin\Customer\Api;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Interception\PluginList\PluginList;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use Wagento\SMSNotifications\Plugin\Customer\Api\CustomerRepositoryInterfacePlugin;

/**
 * Customer Repository Interface Plug-in Test
 *
 * @package Wagento\SMSNotifications\Test\Integration\Plugin\Customer\Api
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @phpcs:disable Magento2.PHP.FinalImplementation.FoundFinal -- Tests are not meant to be extended.
 */
final class CustomerRepositoryInterfacePluginTest extends TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @magentoAppArea frontend
     */
    public function testPluginIsConfigured(): void
    {
        $pluginList = $this->objectManager->create(PluginList::class);
        $plugins = $pluginList->get(CustomerRepositoryInterface::class, []);

        $this->assertArrayHasKey('sms_notifications_attach_subscriptions', $plugins);
        $this->assertSame(
            CustomerRepositoryInterfacePlugin::class,
            $plugins['sms_notifications_attach_subscriptions']['instance']
        );
    }

    /**
     * @magentoConfigFixture default/sms_notifications/general/enabled 1
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testAfterGetAttachesSmsSubscriptionsToCustomer(): void
    {
        $smsSubscriptions = self::getSmsSubscriptionDataFixture();

        try {
            $customer = $this->customerRepository->get('customer@example.com');
        } catch (NoSuchEntityException | LocalizedException $e) {
            $this->fail('Could not retrieve test customer.');
        }

        $this->assertNotNull($customer->getExtensionAttributes()->getSmsNotificationSubscriptions());
        $this->assertSame(
            array_column($smsSubscriptions, 'sms_type'),
            $customer->getExtensionAttributes()->getSmsNotificationSubscriptions()
        );
    }

    /**
     * @magentoConfigFixture default/sms_notifications/general/enabled 1
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testAfterGetByIdAttachesSmsSubscriptionsToCustomer(): void
    {
        $smsSubscriptions = self::getSmsSubscriptionDataFixture();

        try {
            $customer = $this->customerRepository->getById(1);
        } catch (NoSuchEntityException | LocalizedException $e) {
            $this->fail('Could not retrieve test customer.');
        }

        $this->assertNotNull($customer->getExtensionAttributes()->getSmsNotificationSubscriptions());
        $this->assertSame(
            array_column($smsSubscriptions, 'sms_type'),
            $customer->getExtensionAttributes()->getSmsNotificationSubscriptions()
        );
    }

    /**
     * @magentoConfigFixture default/sms_notifications/general/enabled 1
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testAfterGetByListAttachesSmsSubscriptionsToCustomers(): void
    {
        $smsSubscriptions = self::getSmsSubscriptionDataFixture();
        $searchCriteria = $this->objectManager->create(SearchCriteriaBuilder::class)
            ->addFilter('store_id', 1)
            ->create();

        try {
            $customers = $this->customerRepository->getList($searchCriteria)->getItems();
        } catch (LocalizedException $e) {
            $this->fail('Could not retrieve test customers.');
        }

        $this->assertNotNull($customers[0]->getExtensionAttributes()->getSmsNotificationSubscriptions());
        $this->assertSame(
            array_column($smsSubscriptions, 'sms_type'),
            $customers[0]->getExtensionAttributes()->getSmsNotificationSubscriptions()
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();
        $this->customerRepository = $this->objectManager->create(CustomerRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->objectManager = null;
        $this->customerRepository = null;
    }

    private static function getSmsSubscriptionDataFixture(): array
    {
        return require __DIR__ . '/../../../_files/create_sms_subscriptions_from_source.php';
    }
}
