<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Ui\DataProvider
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Ui\DataProvider;

use LinkMobility\SMSNotifications\Model\ResourceModel\SmsSubscription\CollectionFactory;
use LinkMobility\SMSNotifications\Model\Source\SmsType;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * SMS Subscriptions Data Provider
 *
 * @package LinkMobility\SMSNotifications\Ui\DataProvider
 */
final class SmsSubscriptions extends AbstractDataProvider
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    private $backendSession;
    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var \LinkMobility\SMSNotifications\Model\Source\SmsType
     */
    private $smsTypeSource;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \LinkMobility\SMSNotifications\Model\ResourceModel\SmsSubscription\CollectionFactory $collectionFactory
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param array $meta
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        BackendSession $backendSession,
        FilterBuilder $filterBuilder,
        SmsType $smsTypeSource,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $collectionFactory->create();
        $this->backendSession = $backendSession;
        $this->filterBuilder = $filterBuilder;
        $this->smsTypeSource = $smsTypeSource;

        $this->addCustomerFilter();
    }

    public function getData()
    {
        $data = parent::getData();
        $smsTypes = $this->smsTypeSource->toArray();
        $customerId = $this->backendSession->getCustomerData()['customer_id'];
        $usedSmsTypeKeys = [];

        foreach ($data['items'] as &$item) {
            $key = array_search($item['sms_type'], array_column($smsTypes, 'code'));

            if ($key === false) {
                $item['is_active'] = 0;

                continue;
            }

            $item['is_active'] = 1;
            $item['description'] = $smsTypes[$key]['description'];
            $usedSmsTypeKeys[] = $key;
        }

        unset($item);

        $lastItem = end($data['items']);
        $itemId = (int)$lastItem['sms_subscription_id'];

        foreach ($smsTypes as $key => $smsType) {
            if (in_array($key, $usedSmsTypeKeys, true)) {
                continue;
            }

            $data['items'][] = [
                'sms_subscription_id' => ++$itemId,
                'customer_id' => $customerId,
                'is_active' => 0,
                'sms_type' => $smsType['code'],
                'description' => $smsType['description'],
            ];
        }

        $data['totalRecords'] = count($data['items']);

        return $data;
    }

    /**
     * @return string[]
     */
    public function getAllSmsTypes(): array
    {
        return $this->collection->getAllSmsTypes();
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addCustomerFilter(): void
    {
        $customerData = $this->backendSession->getCustomerData() ?? [];

        if (!array_key_exists('customer_id', $customerData)) {
            throw new LocalizedException(__('Could not get ID of customer to retrieve SMS subscriptions for.'));
        }

        $filter = $this->filterBuilder->setField('customer_id')
            ->setValue($customerData['customer_id'])
            ->setConditionType('eq')
            ->create();

        $this->addFilter($filter);
    }
}
