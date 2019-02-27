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

namespace LinkMobility\SMSNotifications\Model;

use LinkMobility\SMSNotifications\Api\ConfigInterface;
use LinkMobility\SMSNotifications\Gateway\ApiClientInterface;
use LinkMobility\SMSNotifications\Gateway\ApiException;
use LinkMobility\SMSNotifications\Gateway\Factory\MessageEntityHydratorFactory;
use LinkMobility\SMSNotifications\Gateway\Factory\MessageFactory;
use LinkMobility\SMSNotifications\Util\TemplateProcessorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Shipping\Helper\Data as ShippingHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Message Service
 *
 * @package LinkMobility\SMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MessageService
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Magento\Shipping\Helper\Data
     */
    private $shippingHelper;
    /**
     * @var \LinkMobility\SMSNotifications\Api\ConfigInterface
     */
    private $config;
    /**
     * @var \LinkMobility\SMSNotifications\Gateway\Factory\MessageFactory
     */
    private $messageFactory;
    /**
     * @var \LinkMobility\SMSNotifications\Gateway\Factory\MessageEntityHydratorFactory
     */
    private $messageEntityHydratorFactory;
    /**
     * @var \LinkMobility\SMSNotifications\Util\TemplateProcessorInterface
     */
    private $templateProcessor;
    /**
     * @var \LinkMobility\SMSNotifications\Gateway\ApiClientInterface
     */
    private $apiClient;
    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    private $order;
    /**
     * @var \Magento\Sales\Api\Data\ShipmentInterface
     */
    private $shipment;

    public function __construct(
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        ScopeConfigInterface $scopeConfig,
        ShippingHelper $shippingHelper,
        ConfigInterface $config,
        MessageFactory $messageFactory,
        MessageEntityHydratorFactory $messageEntityHydratorFactory,
        TemplateProcessorInterface $templateProcessor,
        ApiClientInterface $apiClient
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->shippingHelper = $shippingHelper;
        $this->config = $config;
        $this->messageFactory = $messageFactory;
        $this->messageEntityHydratorFactory = $messageEntityHydratorFactory;
        $this->templateProcessor = $templateProcessor;
        $this->apiClient = $apiClient;
    }

    public function setOrder(OrderInterface $order): MessageService
    {
        $this->order = $order;

        return $this;
    }

    public function setShipment(ShipmentInterface $shipment): MessageService
    {
        $this->shipment = $shipment;

        return $this;
    }

    public function sendMessage(string $message, string $to, string $messageType): bool
    {
        $websiteId = $this->getWebsiteId();
        $messageEntity = $this->messageFactory->create();
        $messageEntityHydrator = $this->messageEntityHydratorFactory->create();
        $source = $this->config->getSource($websiteId);
        $sourceType = $this->config->getSourceType($websiteId);
        $platformId = $this->config->getPlatformId($websiteId);
        $platformPartnerId = $this->config->getPlatformPartnerId($websiteId);
        $processedMessage = $this->processMessage($message, $messageType);

        if ($source === null || $sourceType === null || $platformId === null || $platformPartnerId === null) {
            $this->logger->critical(
                __('The API settings are not configured properly.'),
                [
                    'source' => $source,
                    'sourceType' => $sourceType,
                    'platformId' => $platformId,
                    'platformPartnerId' => $platformPartnerId
                ]
            );

            return false;
        }

        $messageEntity->setSource($source);
        $messageEntity->setSourceTON($sourceType);
        $messageEntity->setDestination($to);
        $messageEntity->setUserData($processedMessage);
        $messageEntity->setPlatformId($platformId);
        $messageEntity->setPlatformPartnerId($platformPartnerId);

        $messageData = array_filter($messageEntityHydrator->extract($messageEntity));

        try {
            $this->apiClient->setUri('send');
            $this->apiClient->setUsername($this->config->getApiUser($websiteId));
            $this->apiClient->setPassword($this->config->getApiPassword($websiteId));
            $this->apiClient->setHttpMethod(ApiClientInterface::HTTP_METHOD_POST);
            $this->apiClient->setData($messageData);
            $this->apiClient->sendRequest();

            $result = $this->apiClient->getResult();

            $this->logger->debug(
                __('The SMS message was sent successfully.'),
                [
                    'message' => $processedMessage,
                    'result' => $result->toArray()
                ]
            );
        } catch (ApiException $e) {
            $this->logger->critical(
                __($e->getMessage()),
                [
                    'message' => $processedMessage,
                    'result' => $e->getResponseData()
                ]
            );

            return false;
        }

        return true;
    }

    private function processMessage(string $message, string $type): string
    {
        $variables = [];

        if ($type === 'order') {
            $variables = $this->getOrderMessageVariables();
        } else {
            return $message;
        }

        return $this->templateProcessor->process($message, $variables);
    }

    private function getOrderMessageVariables(): array
    {
        if ($this->order === null) {
            return [];
        }

        return [
            'order_id' => $this->order->getIncrementId(),
            'order_url' => $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $this->order->getEntityId()]),
            'tracking_url' => $this->getShipmentTrackingUrl(),
            'customer_name' => $this->order->getCustomerFirstname() . ' ' . $this->order->getCustomerLastname(),
            'customer_first_name' => $this->order->getCustomerFirstname(),
            'customer_last_name' => $this->order->getCustomerLastname(),
            'store_name' => $this->getStoreNameById((int)$this->order->getStoreId(), $this->order->getStoreName()),
        ];
    }

    private function getStoreNameById(int $storeId, string $default): string
    {
        try {
            $storeName = $this->scopeConfig->getValue(
                'general/store_information/name',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        } catch (\Exception $e) {
            $storeName = null;
        }

        if ($storeName === null) {
            if (strpos($default, "\n") !== false) {
                $default = explode("\n", $default)[1];
            }

            $storeName = $default;
        }

        return $storeName;
    }

    private function getShipmentTrackingUrl(): string
    {
        if ($this->shipment !== null) {
            $salesModel = $this->shipment;
        } else {
            $salesModel = $this->order;
        }

        if ($salesModel === null) {
            return '';
        }

        return $this->shippingHelper->getTrackingPopupUrlBySalesModel($salesModel);
    }

    private function getWebsiteId(): ?int
    {
        $storeId = null;

        if ($this->order !== null && $this->order->getStoreId()) {
            $storeId = $this->order->getStoreId();
        }

        try {
            $websiteId = (int)$this->storeManager->getStore($storeId)->getWebsiteId();
        } catch (NoSuchEntityException $e) {
            $websiteId = null;
        }

        return $websiteId;
    }
}
