<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\LinkMobilitySMSNotifications\Model\ResourceModel\SmsSubscription\Grid
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\LinkMobilitySMSNotifications\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * SMS Subscription Status Column
 *
 * @package Wagento\LinkMobilitySMSNotifications\Ui\Component\Listing\Column
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class SmsSubscriptionStatus extends Column implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource): array
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $item['status'] = $item['is_active'] ? __('Subscribed') : __('Not Subscribed');
        }

        return $dataSource;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => '0',
                'label' => __('Subscribed')
            ],
            [
                'value' => '1',
                'label' => __('Not Subscribed')
            ]
        ];
    }
}
