<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Plugin\Sales\Model\Order
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Plugin\Sales\Model\Order;

use Wagento\SMSNotifications\Model\SmsSender;
use Magento\Sales\Model\Order\Invoice;

/**
 * Plug-in for {@see \Magento\Sales\Model\Order\Invoice}
 *
 * @package Wagento\SMSNotifications\Plugin\Sales\Model\Order
 * @author Joseph Leedy <joseph@wagento.com>
 */
class InvoicePlugin
{
    /**
     * @var \Wagento\SMSNotifications\Model\SmsSender|\Wagento\SMSNotifications\Model\SmsSender\InvoiceSender
     */
    private $smsSender;

    public function __construct(SmsSender $smsSender)
    {
        $this->smsSender = $smsSender;
    }

    /**
     * @see \Magento\Sales\Model\Order\Invoice::register()
     */
    public function afterRegister(Invoice $subject): Invoice
    {
        $this->smsSender->send($subject);

        return $subject;
    }
}
