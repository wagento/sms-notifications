<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Model\SmsSender;

use Wagento\SMSNotifications\Api\ConfigInterface;
use Wagento\SMSNotifications\Api\SmsSubscriptionRepositoryInterface;
use Wagento\SMSNotifications\Model\MessageService;
use Wagento\SMSNotifications\Model\SmsSender;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Model\Order;
use Magento\Store\Api\StoreRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Order SMS Sender
 *
 * @package Wagento\SMSNotifications\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 */
class OrderSender extends SmsSender
{
    /**
     * @var \Magento\Sales\Api\Data\OrderExtensionFactory
     */
    private $orderExtensionFactory;

    public function __construct(
        LoggerInterface $logger,
        StoreRepositoryInterface $storeRepository,
        CustomerRepositoryInterface $customerRepository,
        ConfigInterface $config,
        SmsSubscriptionRepositoryInterface $subscriptionRepository,
        MessageService $messageService,
        OrderExtensionFactory $orderExtensionFactory
    ) {
        parent::__construct(
            $logger,
            $storeRepository,
            $customerRepository,
            $config,
            $subscriptionRepository,
            $messageService
        );

        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    /**
     * @phpcs:disable Generic.Files.LineLength.TooLong
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order
     */
    public function send(AbstractModel $order): bool
    {
        $storeId = (int)$order->getStoreId();
        $websiteId = $this->getWebsiteIdByStoreId($storeId);
        $orderExtensionAttributes = $order->getExtensionAttributes() ?? $this->orderExtensionFactory->create();
        $orderState = $order->getState();

        if (
            !$this->isModuleEnabled($websiteId)
            || $order->getCustomerIsGuest()
            || $orderExtensionAttributes->getIsSmsNotificationSent() === true
        ) {
            return false;
        }

        if ($orderExtensionAttributes->getIsOrderHoldReleased() === true) {
            $orderState = 'released';
        }

        switch ($orderState) {
            case Order::STATE_NEW:
            case Order::STATE_PROCESSING:
                $messageTemplate = $this->config->getOrderPlacedTemplate($storeId);
                $smsType = 'order_placed';
                break;
            case Order::STATE_CANCELED:
                $messageTemplate = $this->config->getOrderCanceledTemplate($storeId);
                $smsType = 'order_canceled';
                break;
            case Order::STATE_HOLDED:
                $messageTemplate = $this->config->getOrderHeldTemplate($storeId);
                $smsType = 'order_held';
                break;
            case 'released':
                $messageTemplate = $this->config->getOrderReleasedTemplate($storeId);
                $smsType = 'order_released';
                break;
            default:
                return false;
        }

        $customerId = (int)$order->getCustomerId();
        $customer = $this->getCustomerById($customerId);

        if ($customer === null) {
            return false;
        }

        $messageRecipient = $this->getCustomerMobilePhoneNumber($customer);

        if (!in_array($smsType, $this->getCustomerSmsSubscriptions($customerId), true) || $messageRecipient === null) {
            return false;
        }

        $this->messageService->setOrder($order);

        $messageSent = $this->messageService->sendMessage($messageTemplate, $messageRecipient, 'order');

        $orderExtensionAttributes->setIsSmsNotificationSent(true);

        $order->setExtensionAttributes($orderExtensionAttributes);

        return $messageSent;
    }
}
