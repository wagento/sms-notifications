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
use LinkMobility\SMSNotifications\Gateway\Factory\MessageFactory;
use LinkMobility\SMSNotifications\Util\TemplateProcessorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Shipping\Helper\Data as ShippingHelper;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Zend\Hydrator\Reflection as MessageHydrator;

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
     * @var \Zend\Hydrator\Reflection
     */
    private $messageHydrator;
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
        MessageHydrator $messageHydrator,
        UrlInterface $urlBuilder,
        ScopeConfigInterface $scopeConfig,
        ShippingHelper $shippingHelper,
        ConfigInterface $config,
        MessageFactory $messageFactory,
        TemplateProcessorInterface $templateProcessor,
        ApiClientInterface $apiClient
    ) {
        $this->logger = $logger;
        $this->messageHydrator = $messageHydrator;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->shippingHelper = $shippingHelper;
        $this->config = $config;
        $this->messageFactory = $messageFactory;
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
        $messageEntity = $this->messageFactory->create();
        $sourceNumber = $this->config->getSourceNumber();
        $platformId = $this->config->getPlatformId();
        $platformPartnerId = $this->config->getPlatformPartnerId();
        $processedMessage = $this->processMessage($message, $messageType);

        $messageEntity->setSource($sourceNumber);
        $messageEntity->setDestination($to);
        $messageEntity->setUserData($processedMessage);
        $messageEntity->setPlatformId($platformId);
        $messageEntity->setPlatformPartnerId($platformPartnerId);

        $messageData = array_filter($this->messageHydrator->extract($messageEntity));

        try {
            $this->apiClient->setUri('send');
            $this->apiClient->setUsername($this->config->getApiUser());
            $this->apiClient->setPassword($this->config->getApiPassword());
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
}
