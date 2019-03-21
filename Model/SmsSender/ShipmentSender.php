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
use Magento\Sales\Api\Data\ShipmentExtensionFactory;
use Magento\Store\Api\StoreRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Shipment SMS Sender
 *
 * @package LinkMobility\SMSNotifications\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 *
 * @phpcs:disable Generic.Files.LineLength.TooLong
 */
final class ShipmentSender extends SmsSender
{
    /**
     * @var \Magento\Sales\Api\Data\ShipmentExtensionFactory
     */
    private $shipmentExtensionFactory;

    public function __construct(
        LoggerInterface $logger,
        StoreRepositoryInterface $storeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerRepositoryInterface $customerRepository,
        ConfigInterface $config,
        SmsSubscriptionRepositoryInterface $subscriptionRepository,
        MessageService $messageService,
        ShipmentExtensionFactory $shipmentExtensionFactory
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

        $this->shipmentExtensionFactory = $shipmentExtensionFactory;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Sales\Api\Data\ShipmentInterface|\Magento\Sales\Model\Order\Shipment $shipment
     */
    public function send(AbstractModel $shipment): bool
    {
        $storeId = (int)$shipment->getStoreId();
        $websiteId = $this->getWebsiteIdByStoreId($storeId);
        /** @var \Magento\Sales\Api\Data\ShipmentExtensionInterface $shipmentExtension */
        $shipmentExtension = $shipment->getExtensionAttributes() ?? $this->shipmentExtensionFactory->create();

        if (
            !$this->isModuleEnabled($websiteId)
            || $shipment->getOrder()->getCustomerIsGuest()
            || $shipmentExtension->getIsSmsNotificationSent() === true
        ) {
            return false;
        }

        $customerId = (int)$shipment->getCustomerId();
        $customer = $this->getCustomerById($customerId);

        if ($customer === null) {
            return false;
        }

        $messageRecipient = $this->getCustomerMobilePhoneNumber($customer);

        if (
            !in_array('order_shipped', $this->getCustomerSmsSubscriptions($customerId), true)
            || $messageRecipient === null
        ) {
            return false;
        }

        $this->messageService->setShipment($shipment);
        $this->messageService->setOrder($shipment->getOrder());

        $messageTemplate = $this->config->getOrderShippedTemplate($storeId);
        $messageSent = $this->messageService->sendMessage($messageTemplate, $messageRecipient, 'shipment');

        $shipmentExtension->setIsSmsNotificationSent(true);

        $shipment->setExtensionAttributes($shipmentExtension);

        return $messageSent;
    }
}
