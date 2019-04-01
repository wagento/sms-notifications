<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\LinkMobilitySMSNotifications\Model\Config\Source
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\LinkMobilitySMSNotifications\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Source Type Configuration Field Source Model
 *
 * @package Wagento\LinkMobilitySMSNotifications\Model\Config\Source
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class SourceType implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'SHORTNUMBER',
                'label' => __('Short Number')
            ],
            [
                'value' => 'ALPHANUMERIC',
                'label' => __('Alphanumeric')
            ],
            [
                'value' => 'MSISDN',
                'label' => __('Phone Number')
            ]
        ];
    }
}
