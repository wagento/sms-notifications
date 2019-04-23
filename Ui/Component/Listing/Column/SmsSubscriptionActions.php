<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Ui\Component\Listing\Column
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * SMS Subscription Actions Listing Column
 *
 * @package Wagento\SMSNotifications\Ui\Component\Listing\Column
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class SmsSubscriptionActions extends Column
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->urlBuilder = $urlBuilder;
    }

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
            if ($item['is_active'] === 1) {
                $item[$this->getData('name')]['unsubscribe'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'sms_notifications/subscription/delete',
                        ['sms_subscription_id' => $item['sms_subscription_id']]
                    ),
                    'label' => __('Unsubscribe'),
                    'confirm' => [
                        'title' => __('Delete SMS Subscription'),
                        'message' => __('Are you sure that you want to delete this SMS Subscription?')
                    ],
                    'post' => true
                ];
            } else {
                $item[$this->getData('name')]['subscribe'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'sms_notifications/subscription/create',
                        ['sms_type' => $item['sms_type']]
                    ),
                    'label' => __('Subscribe'),
                ];
            }
        }

        return $dataSource;
    }
}
