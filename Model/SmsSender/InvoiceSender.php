<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\LinkMobilitySMSNotifications\Model
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
use Magento\Sales\Api\Data\InvoiceExtensionFactory;
use Magento\Store\Api\StoreRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Invoice SMS Sender
 *
 * @package Wagento\LinkMobilitySMSNotifications\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 */
class InvoiceSender extends SmsSender
{
    /**
     * @var \Magento\Sales\Api\Data\InvoiceExtensionFactory
     */
    private $invoiceExtensionFactory;

    public function __construct(
        LoggerInterface $logger,
        StoreRepositoryInterface $storeRepository,
        CustomerRepositoryInterface $customerRepository,
        ConfigInterface $config,
        SmsSubscriptionRepositoryInterface $subscriptionRepository,
        MessageService $messageService,
        InvoiceExtensionFactory $invoiceExtensionFactory
    ) {
        parent::__construct(
            $logger,
            $storeRepository,
            $customerRepository,
            $config,
            $subscriptionRepository,
            $messageService
        );

        $this->invoiceExtensionFactory = $invoiceExtensionFactory;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Sales\Model\Order\Invoice $invoice
     */
    public function send(AbstractModel $invoice): bool
    {
        $storeId = (int)$invoice->getStoreId();
        $websiteId = $this->getWebsiteIdByStoreId($storeId);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $invoice->getOrder();
        /** @var \Magento\Sales\Api\Data\InvoiceExtensionInterface $invoiceExtension */
        $invoiceExtension = $invoice->getExtensionAttributes() ?? $this->invoiceExtensionFactory->create();

        if (
            !$this->isModuleEnabled($websiteId)
            || (bool)$order->getCustomerIsGuest()
            || $invoiceExtension->getIsSmsNotificationSent() === true
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
            !in_array('order_invoiced', $this->getCustomerSmsSubscriptions($customerId), true)
            || $messageRecipient === null
        ) {
            return false;
        }

        $this->messageService->setInvoice($invoice);

        $messageTemplate = $this->config->getOrderInvoicedTemplate($storeId);
        $messageSent = $this->messageService->sendMessage($messageTemplate, $messageRecipient, 'invoice');

        $invoiceExtension->setIsSmsNotificationSent(true);

        $invoice->setExtensionAttributes($invoiceExtension);

        return $messageSent;
    }
}
