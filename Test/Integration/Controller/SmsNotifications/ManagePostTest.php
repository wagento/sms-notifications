<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Test\Integration\Controller\SmsNotifications
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

namespace Linkmobility\Notifications\Test\Integration\Controller\SmsNotifications;

use Linkmobility\Notifications\Api\SmsSubscriptionRepositoryInterface;
use Linkmobility\Notifications\Model\SmsSubscription;
use Linkmobility\Notifications\Model\SmsSubscriptionRepository;
use Linkmobility\Notifications\Model\Source\SmsType as SmsTypeSource;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Api\ImageProcessor;
use Magento\Framework\Api\ImageProcessorInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractController as AbstractControllerTestCase;
use Psr\Log\Test\TestLogger;

/**
 * Manage SMS Subscriptions POST Action Controller Test
 *
 * @package Linkmobility\Notifications\Test\Integration\Controller\SmsNotifications
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @phpcs:disable Generic.Files.LineLength.TooLong
 */
class ManagePostTest extends AbstractControllerTestCase
{
    private const ACTION_URI = 'customer/smsnotifications/managePost';
    private const REDIRECT_URI = 'customer/smsnotifications/manage';

    /**
     * @magentoAppArea frontend
     */
    public function testInvalidCustomerIdRetursErrorMessage()
    {
        $this->dispatch(self::ACTION_URI);

        $this->assertSessionMessages(
            $this->equalTo([__('Something went wrong while saving your text notification preferences.')]),
            MessageInterface::TYPE_ERROR
        );
        $this->assertTrue(
            $this->_objectManager->get(TestLogger::class)->hasCriticalThatPasses(function ($record) {
                return (string)$record['message'] === (string)__('Could not get ID of customer to save SMS preferences for.');
            })
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSubscribeToAllNotificationsReturnsSuccessMessage()
    {
        $smsTypes = array_column((new SmsTypeSource())->toArray(), 'code');

        $this->getRequest()->setPostValue('sms_types', array_fill_keys(array_values($smsTypes), '1'));
        $this->loginCustomer(1);
        $this->dispatch(self::ACTION_URI);

        $this->assertSessionMessages(
            $this->equalTo([__('You have been subscribed to %1 text notifications.', count($smsTypes))]),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture createSmsSubscriptionsFixtureProvider
     */
    public function testUnsubscribeFromAllNotificationsReturnsSuccessMessage()
    {
        $smsTypes = array_column((new SmsTypeSource())->toArray(), 'code');

        $this->loginCustomer(1);
        $this->dispatch(self::ACTION_URI);

        $this->assertSessionMessages(
            $this->equalTo([__('You have been unsubscribed from %1 text notifications.', count($smsTypes))]),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSubscribeAndUnsubscribeFromNotificationsReturnsSuccessMessages()
    {
        $existingSubscriptions = self::createSmsSubscriptionsFixtureProvider(3);
        $smsTypes = array_slice(array_column((new SmsTypeSource())->toArray(), 'code'), 4);

        $this->getRequest()->setPostValue('sms_types', array_fill_keys(array_values($smsTypes), '1'));
        $this->loginCustomer(1);
        $this->dispatch(self::ACTION_URI);

        $this->assertSessionMessages(
            $this->equalTo([
                __('You have been unsubscribed from %1 text notifications.', count($existingSubscriptions)),
                __('You have been subscribed to %1 text notifications.', count($smsTypes)),
            ]),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testUnsubscribeFromNotificationsReturnsErrorMessage()
    {
        $this->createDeletedSubcriptionMocks();

        $this->loginCustomer(1);
        $this->dispatch(self::ACTION_URI);

        $this->assertSessionMessages(
            $this->equalTo([__('You could not be unsubscribed from 1 text notification.')]),
            MessageInterface::TYPE_ERROR
        );
        $this->assertTrue(
            $this->_objectManager->get(TestLogger::class)->hasCriticalThatPasses(function ($record) {
                return (string)$record['message'] === (string)__('Could not delete SMS subscription for customer. Error: %1', 'Unknown error');
            })
        );
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSubscribeAndUnsubscribeFromNotificationsReturnsSuccessAndErrorMessage()
    {
        $this->createDeletedSubcriptionMocks();

        $this->getRequest()->setPostValue('sms_types', ['order_shipped' => '1']);
        $this->loginCustomer(1);
        $this->dispatch(self::ACTION_URI);

        $this->assertSessionMessages(
            $this->equalTo([__('You could not be unsubscribed from 1 text notification.')]),
            MessageInterface::TYPE_ERROR
        );
        $this->assertSessionMessages(
            $this->equalTo([__('You have been subscribed to 1 text notification.')]),
            MessageInterface::TYPE_SUCCESS
        );
        $this->assertTrue(
            $this->_objectManager->get(TestLogger::class)->hasCriticalThatPasses(function ($record) {
                return (string)$record['message'] === (string)__('Could not delete SMS subscription for customer. Error: %1', 'Unknown error');
            })
        );
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testUpdateMobileNumberReturnsSuccessMessage()
    {
        $this->getRequest()->setPostValue('sms_mobile_phone_prefix', 'US_1');
        $this->getRequest()->setPostValue('sms_mobile_phone_number', '5555551234');
        $this->loginCustomer(1);
        $this->dispatch(self::ACTION_URI);

        $this->assertSessionMessages(
            $this->equalTo([__('Your mobile telephone number has been updated.')]),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testUpdateMobileNumberReturnsErrorMessage()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|\Magento\Framework\Api\ImageProcessorInterface $imageProcessorMock */
        $imageProcessorMock = $this->getMockBuilder(ImageProcessorInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['save'])
            ->getMockForAbstractClass();

        $imageProcessorMock->method('save')->willThrowException(new LocalizedException(__('Unknown error')));

        $this->_objectManager->addSharedInstance($imageProcessorMock, ImageProcessor::class);

        $this->getRequest()->setPostValue('sms_mobile_phone_prefix', 'US_1');
        $this->getRequest()->setPostValue('sms_mobile_phone_number', '5555551234');
        $this->loginCustomer(1);
        $this->dispatch(self::ACTION_URI);

        $this->assertSessionMessages(
            $this->equalTo([__('Your mobile telephone number could not be updated.')]),
            MessageInterface::TYPE_ERROR
        );
        $this->assertTrue(
            $this->_objectManager->get(TestLogger::class)->hasCriticalThatPasses(function ($record) {
                return (string)$record['message'] === (string)__('Could not save mobile telephone number. Error: %1', 'Unknown error');
            })
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function createSmsSubscriptionsFixtureProvider(int $count = -1)
    {
        return require __DIR__ . '/../../_files/create_sms_subscriptions_from_source.php';
    }

    protected function setUp()
    {
        parent::setUp();

        $this->_objectManager->addSharedInstance(
            $this->_objectManager->get(TestLogger::class),
            'Linkmobility\Notifications\Logger\Logger'
        );

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
    }

    private function loginCustomer(int $customerId)
    {
        /** @var \Magento\Customer\Model\Session $session */
        $session = $this->_objectManager->get(CustomerSession::class);

        $session->loginById($customerId);
    }

    private function createDeletedSubcriptionMocks()
    {
        $smsSubscriptionMock = $this->getMockBuilder(SmsSubscription::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getSmsType', '__isset', '__get'])
            ->getMock();
        $searchResultsMock = $this->getMockBuilder(SearchResultsInterface::class)
            ->setMethods(['getItems'])
            ->getMockForAbstractClass();
        $smsSubscriptionRepositoryMock = $this->getMockBuilder(SmsSubscriptionRepositoryInterface::class)
            ->setMethods(['getList', 'deleteById'])
            ->getMockForAbstractClass();

        $smsSubscriptionMock->method('getId')->willReturn(1);
        $smsSubscriptionMock->method('getSmsType')->willReturn('order_placed');
        $smsSubscriptionMock->method('__isset')->with('sms_type')->willReturn(true);
        $smsSubscriptionMock->method('__get')->with('sms_type')->willReturn('order_placed');

        $searchResultsMock->method('getItems')->willReturn([$smsSubscriptionMock]);

        $smsSubscriptionRepositoryMock->method('getList')->willReturn($searchResultsMock);
        $smsSubscriptionRepositoryMock->method('deleteById')->willThrowException(
            new CouldNotDeleteException(__('Unknown error'))
        );

        $this->_objectManager->addSharedInstance($smsSubscriptionRepositoryMock, SmsSubscriptionRepository::class);
    }
}
