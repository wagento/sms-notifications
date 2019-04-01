<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\LinkMobilitySMSNotifications\Factory
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\LinkMobilitySMSNotifications\Factory;

use Wagento\LinkMobilitySMSNotifications\Api\MessageVariablesInterface;
use Wagento\LinkMobilitySMSNotifications\Model\MessageVariables\InvoiceVariables;
use Wagento\LinkMobilitySMSNotifications\Model\MessageVariables\OrderVariables;
use Wagento\LinkMobilitySMSNotifications\Model\MessageVariables\CustomerVariables;
use Wagento\LinkMobilitySMSNotifications\Model\MessageVariables\ShipmentVariables;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;

/**
 * Message Variables Factory
 *
 * @package Wagento\LinkMobilitySMSNotifications\Factory
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 */
final class MessageVariablesFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(string $type, array $arguments = []): ?MessageVariablesInterface
    {
        $messageVariables = null;

        switch ($type) {
            case 'customer':
                $messageVariables = $this->createCustomerVariables($arguments);
                break;
            case 'invoice':
                $messageVariables = $this->createInvoiceVariables($arguments);
                break;
            case 'order':
                $messageVariables = $this->createOrderVariables($arguments);
                break;
            case 'shipment':
                $messageVariables = $this->createShipmentVariables($arguments);
                break;
            default:
                break;
        }

        return $messageVariables;
    }

    private function createCustomerVariables(array $arguments = []): CustomerVariables
    {
        $customerVariables = $this->objectManager->create(CustomerVariables::class);

        if (array_key_exists('customer', $arguments) && ($arguments['customer'] instanceof CustomerInterface)) {
            $customerVariables->setCustomer($arguments['customer']);
        }

        return $customerVariables;
    }

    private function createInvoiceVariables(array $arguments = []): InvoiceVariables
    {
        $invoiceVariables = $this->objectManager->create(InvoiceVariables::class);

        if (array_key_exists('invoice', $arguments) && ($arguments['invoice'] instanceof InvoiceInterface)) {
            $invoiceVariables->setInvoice($arguments['invoice']);
        }

        return $invoiceVariables;
    }

    private function createOrderVariables(array $arguments = []): OrderVariables
    {
        $orderVariables = $this->objectManager->create(OrderVariables::class);

        if (array_key_exists('order', $arguments) && ($arguments['order'] instanceof Order)) {
            $orderVariables->setOrder($arguments['order']);
        }

        return $orderVariables;
    }

    private function createShipmentVariables(array $arguments = []): ShipmentVariables
    {
        $shipmentVariables = $this->objectManager->create(ShipmentVariables::class);

        if (array_key_exists('shipment', $arguments) && ($arguments['shipment'] instanceof Shipment)) {
            $shipmentVariables->setShipment($arguments['shipment']);
        }

        return $shipmentVariables;
    }
}
