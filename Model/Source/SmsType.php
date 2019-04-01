<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Ui\Component\Listing\Column
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * SMS Notification Type Option Source Model
 *
 * @package LinkMobility\SMSNotifications\Model\Source
 * @author Joseph Leedy <joseph@wagento.com>
 */
class SmsType implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        $smsTypes = $this->toArray();
        $options = [];

        foreach ($smsTypes as $type) {
            $options[] = [
                'value' => $type['code'],
                'label' => $type['description']
            ];
        }

        return $options;
    }

    public function toArray(): array
    {
        return [
            /*[
                'group' => 'general',
                'code' => 'all',
                'description' => __('All notifications')
            ],*/
            [
                'group' => 'order',
                'code' => 'order_placed',
                'description' => __('Order placed successfully')
            ],
            [
                'group' => 'order',
                'code' => 'order_invoiced',
                'description' => __('Order invoiced')
            ],
            [
                'group' => 'order',
                'code' => 'order_shipped',
                'description' => __('Order shipped')
            ],
            [
                'group' => 'order',
                'code' => 'order_refunded',
                'description' => __('Order refunded')
            ],
            [
                'group' => 'order',
                'code' => 'order_canceled',
                'description' => __('Order canceled')
            ],
            [
                'group' => 'order',
                'code' => 'order_held',
                'description' => __('Order placed on hold')
            ],
            [
                'group' => 'order',
                'code' => 'order_released',
                'description' => __('Order hold released')
            ],
        ];
    }
}
