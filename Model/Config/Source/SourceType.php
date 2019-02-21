<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Model\Config\Source
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Source Type Configuration Field Source Model
 *
 * @package LinkMobility\SMSNotifications\Model\Config\Source
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
