<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Model
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
use Magento\Sales\Api\Data\InvoiceExtensionFactory;
use Magento\Store\Api\StoreRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Invoice SMS Sender
 *
 * @package LinkMobility\SMSNotifications\Model\SmsSender
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
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerRepositoryInterface $customerRepository,
        ConfigInterface $config,
        SmsSubscriptionRepositoryInterface $subscriptionRepository,
        MessageService $messageService,
        InvoiceExtensionFactory $invoiceExtensionFactory
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
