<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\LinkMobilitySMSNotifications\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\LinkMobilitySMSNotifications\Model\SmsSender;

use Wagento\LinkMobilitySMSNotifications\Api\ConfigInterface;
use Wagento\LinkMobilitySMSNotifications\Api\SmsSubscriptionRepositoryInterface;
use Wagento\LinkMobilitySMSNotifications\Model\MessageService;
use Wagento\LinkMobilitySMSNotifications\Model\SmsSender;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Api\Data\CreditmemoExtensionFactory;
use Magento\Store\Api\StoreRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Credit Memo SMS Sender
 *
 * @package Wagento\LinkMobilitySMSNotifications\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 *
 * @phpcs:disable Generic.Files.LineLength.TooLong
 */
class CreditmemoSender extends SmsSender
{
    /**
     * @var \Magento\Sales\Api\Data\CreditmemoExtensionFactory
     */
    private $creditmemoExtensionFactory;

    public function __construct(
        LoggerInterface $logger,
        StoreRepositoryInterface $storeRepository,
        CustomerRepositoryInterface $customerRepository,
        ConfigInterface $config,
        SmsSubscriptionRepositoryInterface $subscriptionRepository,
        MessageService $messageService,
        CreditmemoExtensionFactory $creditmemoExtensionFactory
    ) {
        parent::__construct(
            $logger,
            $storeRepository,
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
        $customer = $this->getCustomerById($customerId);

        if ($customer === null) {
            return false;
        }

        $messageRecipient = $this->getCustomerMobilePhoneNumber($customer);

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
