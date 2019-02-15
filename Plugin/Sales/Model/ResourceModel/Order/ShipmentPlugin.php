<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Plugin\Sales\Model\ResourceModel\Order
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Plugin\Sales\Model\ResourceModel\Order;

use Linkmobility\Notifications\Model\SmsSender;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Order\Shipment as ShipmentResource;

/**
 * Plug-in for {@see \Magento\Sales\Model\ResourceModel\Order\Shipment}
 *
 * @package Linkmobility\Notifications\Plugin\Sales\Model\Order
 * @author Joseph Leedy <joseph@wagento.com>
 */
class ShipmentPlugin
{
    /**
     * @var \Linkmobility\Notifications\Model\SmsSender\ShipmentSender
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
