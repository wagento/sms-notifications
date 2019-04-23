<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Plugin\Sales\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Plugin\Sales\Model;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Model\Order;

/**
 * Plug-in for {@see \Magento\Sales\Model\Order}
 *
 * @package Wagento\SMSNotifications\Plugin\Sales\Model
 * @author Joseph Leedy <joseph@wagento.com>
 */
class OrderPlugin
{
    /**
     * @var \Magento\Sales\Api\Data\OrderExtensionFactory
     */
    private $orderExtensionFactory;

    public function __construct(OrderExtensionFactory $orderExtensionFactory)
    {
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    public function afterUnhold(Order $subject): Order
    {
        $orderExtension = $subject->getExtensionAttributes() ?? $this->orderExtensionFactory->create();

        $orderExtension->setIsOrderHoldReleased(true);

        $subject->setExtensionAttributes($orderExtension);

        return $subject;
    }
}
