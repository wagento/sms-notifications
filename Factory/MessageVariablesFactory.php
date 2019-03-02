<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Factory
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Factory;

use LinkMobility\SMSNotifications\Api\MessageVariablesInterface;
use LinkMobility\SMSNotifications\Model\MessageVariables\OrderVariables;
use LinkMobility\SMSNotifications\Model\MessageVariables\CustomerVariables;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;

/**
 * Message Variables Factory
 *
 * @package LinkMobility\SMSNotifications\Factory
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
            case 'order':
                $messageVariables = $this->createOrderVariables($arguments);
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

    private function createOrderVariables(array $arguments = []): OrderVariables
    {
        $orderVariables = $this->objectManager->create(OrderVariables::class);

        if (array_key_exists('order', $arguments) && ($arguments['order'] instanceof Order)) {
            $orderVariables->setOrder($arguments['order']);
        }

        if (array_key_exists('shipment', $arguments) && ($arguments['shipment'] instanceof Shipment)) {
            $orderVariables->setShipment($arguments['shipment']);
        }

        return $orderVariables;
    }
}
