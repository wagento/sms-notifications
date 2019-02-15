<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Model\SmsSender;

use Linkmobility\Notifications\Api\ConfigInterface;
use Linkmobility\Notifications\Api\SmsSubscriptionRepositoryInterface;
use Linkmobility\Notifications\Model\MessageService;
use Linkmobility\Notifications\Model\SmsSender;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Model\Order;
use Magento\Store\Api\StoreRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Order SMS Sender
 *
 * @package Linkmobility\Notifications\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 */
final class OrderSender extends SmsSender
{
    /**
     * @var \Magento\Sales\Api\Data\OrderExtensionFactory
     */
    private $orderExtensionFactory;

    public function __construct(
        LoggerInterface $logger,
        StoreRepositoryInterface $storeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerRepositoryInterface $customerRepository,
        ConfigInterface $config,
        SmsSubscriptionRepositoryInterface $subscriptionRepository,
        MessageService $messageService,
        OrderExtensionFactory $orderExtensionFactory
    ) {
        parent::__construct(
            $logger,
            $storeRepository,
            $searchCriteriaBuilder,
            $customerRepository,
            $config,
            $subscriptionRepository,
            $messageService
        );

        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order
     */
    public function send(AbstractModel $order): bool
    {
        $websiteId = $this->getWebsiteIdByStoreId($order->getStoreId());
        $orderExtensionAttributes = $order->getExtensionAttributes() ?? $this->orderExtensionFactory->create();

        if ($orderExtensionAttributes->getIsOrderHoldReleased() === true) {
            $orderState = 'released';
        } else {
            $orderState = $order->getState();
        }

        if (
            !$this->isModuleEnabled($websiteId)
            || $order->getCustomerIsGuest()
            || $orderExtensionAttributes->getIsSmsNotificationSent() === true
            || $orderState === null
            || $orderState === Order::STATE_CLOSED
        ) {
            return false;
        }

        switch ($orderState) {
            case Order::STATE_NEW:
                $messageTemplate = $this->config->getOrderPlacedTemplate($websiteId);
                $smsType = 'order_placed';
                break;
            case Order::STATE_CANCELED:
                $messageTemplate = $this->config->getOrderCanceledTemplate($websiteId);
                $smsType = 'order_canceled';
                break;
            case Order::STATE_HOLDED:
                $messageTemplate = $this->config->getOrderHeldTemplate($websiteId);
                $smsType = 'order_held';
                break;
            case 'released':
                $messageTemplate = $this->config->getOrderReleasedTemplate($websiteId);
                $smsType = 'order_released';
                break;
            case Order::STATE_PROCESSING:
            default:
                $messageTemplate = $this->config->getOrderUpdatedTemplate($websiteId);
                $smsType = 'order_updated';
        }

        $customerId = (int)$order->getCustomerId();
        $messageRecipient = $this->getCustomerMobilePhoneNumber($customerId);

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
