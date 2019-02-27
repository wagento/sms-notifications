<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Model\SmsSender;

use LinkMobility\SMSNotifications\Api\ConfigInterface;
use LinkMobility\SMSNotifications\Api\SmsSubscriptionRepositoryInterface;
use LinkMobility\SMSNotifications\Model\MessageService;
use LinkMobility\SMSNotifications\Model\SmsSender;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Api\Data\CreditmemoExtensionFactory;
use Magento\Store\Api\StoreRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Credit Memo SMS Sender
 *
 * @package LinkMobility\SMSNotifications\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 */
final class CreditmemoSender extends SmsSender
{
    /**
     * @var \Magento\Sales\Api\Data\CreditmemoExtensionFactory
     */
    private $creditmemoExtensionFactory;

    public function __construct(
        LoggerInterface $logger,
        StoreRepositoryInterface $storeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerRepositoryInterface $customerRepository,
        ConfigInterface $config,
        SmsSubscriptionRepositoryInterface $subscriptionRepository,
        MessageService $messageService,
        CreditmemoExtensionFactory $creditmemoExtensionFactory
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

        $this->creditmemoExtensionFactory = $creditmemoExtensionFactory;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Sales\Api\Data\CreditmemoInterface|\Magento\Sales\Model\Order\Creditmemo $creditmemo
     */
    public function send(AbstractModel $creditmemo): bool
    {
        $storeId = (int)$creditmemo->getStoreId();
        $websiteId = $this->getWebsiteIdByStoreId($storeId);
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $creditmemo->getOrder();
        $creditmemoExtensionAttributes = $creditmemo->getExtensionAttributes() ?? $this->creditmemoExtensionFactory
                ->create();

        if (
            !$this->isModuleEnabled($websiteId)
            || $order->getCustomerIsGuest()
            || $creditmemoExtensionAttributes->getIsSmsNotificationSent() === true
        ) {
            return false;
        }

        $customerId = (int)$order->getCustomerId();
        $messageRecipient = $this->getCustomerMobilePhoneNumber($customerId);

        if (
            !in_array('order_refunded', $this->getCustomerSmsSubscriptions($customerId), true)
            || $messageRecipient === null
        ) {
            return false;
        }

        $this->messageService->setOrder($order);

        $messageTemplate = $this->config->getOrderRefundedTemplate($storeId);
        $messageSent = $this->messageService->sendMessage($messageTemplate, $messageRecipient, 'order');

        $creditmemoExtensionAttributes->setIsSmsNotificationSent(true);

        $creditmemo->setExtensionAttributes($creditmemoExtensionAttributes);

        return $messageSent;
    }
}
