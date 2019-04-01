<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\LinkMobilitySMSNotifications\Plugin\Sales\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\LinkMobilitySMSNotifications\Plugin\Sales\Api;

use Wagento\LinkMobilitySMSNotifications\Model\SmsSender;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Plug-in for {@see \Magento\Sales\Api\OrderRepositoryInterface}
 *
 * @package Wagento\LinkMobilitySMSNotifications\Plugin\Sales\Api
 * @author Joseph Leedy <joseph@wagento.com>
 */
class OrderRepositoryInterfacePlugin
{
    /**
     * @var \Wagento\LinkMobilitySMSNotifications\Model\SmsSender|\Wagento\LinkMobilitySMSNotifications\Model\SmsSender\OrderSender
     */
    private $smsSender;

    public function __construct(SmsSender $smsSender)
    {
        $this->smsSender = $smsSender;
    }

    public function afterSave(OrderRepositoryInterface $subject, OrderInterface $order): OrderInterface
    {
        $this->smsSender->send($order);

        return $order;
    }
}
