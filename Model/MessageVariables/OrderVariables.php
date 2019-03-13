<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Model\MessageVariables
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Model\MessageVariables;

use LinkMobility\SMSNotifications\Api\MessageVariablesInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface as UrlBuilder;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Helper\Data as ShippingHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Order Message Variables
 *
 * @package LinkMobility\SMSNotifications\Model\MessageVariables
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class OrderVariables implements MessageVariablesInterface
{
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
     * @var \Magento\Sales\Model\Order
     */
    private $order;
    /**
     * @var \Magento\Sales\Model\Order\Shipment
     */
    private $shipment;

    public function __construct(
        UrlBuilder $urlBuilder,
        ScopeConfigInterface $scopeConfig,
        ShippingHelper $shippingHelper
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->shippingHelper = $shippingHelper;
    }

    public function getVariables(): array
    {
        if ($this->order === null) {
            return [];
        }

        return [
            'order_id' => $this->order->getIncrementId(),
            'order_url' => $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $this->order->getEntityId()]),
            'tracking_numbers' => $this->getShipmentTrackingNumbers(),
            'customer_name' => $this->order->getCustomerFirstname() . ' ' . $this->order->getCustomerLastname(),
            'customer_first_name' => $this->order->getCustomerFirstname(),
            'customer_last_name' => $this->order->getCustomerLastname(),
            'store_name' => $this->getStoreNameById((int)$this->order->getStoreId(), $this->order->getStoreName()),
        ];
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function setShipment(Shipment $shipment): self
    {
        $this->shipment = $shipment;

        return $this;
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

    private function getShipmentTrackingNumbers(): string
    {
        $trackingNumbers = [];

        if ($this->shipment === null) {
            return '';
        }

        $tracks = $this->shipment->getAllTracks();

        /** @var \Magento\Shipping\Model\Order\Track $track */
        foreach ($tracks as $track) {
            $trackingNumbers[] = $track->getTitle() . ': ' . $track->getNumber();
        }

        return implode($trackingNumbers, ', ');
    }
}
