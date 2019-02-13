<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Model\SmsSender
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
use Magento\Sales\Api\Data\CreditmemoExtensionFactory;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Credit Memo SMS Sender
 *
 * @package Linkmobility\Notifications\Model\SmsSender
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

    public function send(CreditmemoInterface $creditmemo): bool
    {
        $websiteId = $this->getWebsiteIdByStoreId($creditmemo->getStoreId());
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

        $messageTemplate = $this->config->getOrderRefundedTemplate($websiteId);
        $messageSent = $this->messageService->sendMessage($messageTemplate, $messageRecipient, 'order');

        $creditmemoExtensionAttributes->setIsSmsNotificationSent(true);

        $creditmemo->setExtensionAttributes($creditmemoExtensionAttributes);

        return $messageSent;
    }
}
