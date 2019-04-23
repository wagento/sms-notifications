<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Plugin\Sales\Model\ResourceModel\Order
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Plugin\Sales\Model\ResourceModel\Order;

use Wagento\SMSNotifications\Model\SmsSender;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Order\Shipment as ShipmentResource;

/**
 * Plug-in for {@see \Magento\Sales\Model\ResourceModel\Order\Shipment}
 *
 * @package Wagento\SMSNotifications\Plugin\Sales\Model\ResourceModel\Order
 * @author Joseph Leedy <joseph@wagento.com>
 */
class ShipmentPlugin
{
    /**
     * @var \Wagento\SMSNotifications\Model\SmsSender|\Wagento\SMSNotifications\Model\SmsSender\ShipmentSender
     */
    private $smsSender;

    public function __construct(SmsSender $smsSender)
    {
        $this->smsSender = $smsSender;
    }

    public function afterSave(ShipmentResource $subject, ShipmentResource $result, Shipment $shipment): ShipmentResource
    {
        $this->smsSender->send($shipment);

        return $result;
    }
}
