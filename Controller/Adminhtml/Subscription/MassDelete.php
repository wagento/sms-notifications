<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Controller\Adminhtml\Subscription
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Controller\Adminhtml\Subscription;

use Wagento\SMSNotifications\Api\SmsSubscriptionManagementInterface;
use Wagento\SMSNotifications\Model\ResourceModel\SmsSubscription\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

/**
 * Bulk Delete SMS Subscription Action
 *
 * @package Wagento\SMSNotifications\Controller\Adminhtml\Subscription
 * @author Joseph Leedy <joseph@wagento.com>
 */
class MassDelete extends Action
{
    const ADMIN_RESOURCE = 'Wagento_SMSNotifications::manage_sms_subscriptions';

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    private $filter;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Wagento\SMSNotifications\Model\ResourceModel\SmsSubscription\CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var \Wagento\SMSNotifications\Api\SmsSubscriptionManagementInterface
     */
    private $smsSubscriptionManagement;

    public function __construct(
        Context $context,
        Filter $filter,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        SmsSubscriptionManagementInterface $smsSubscriptionManagement
    ) {
        parent::__construct($context);

        $this->filter = $filter;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->smsSubscriptionManagement = $smsSubscriptionManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (
            !$this->_getSession()->hasCustomerData()
            || !array_key_exists('customer_id', $this->_getSession()->getCustomerData())
        ) {
            $this->messageManager->addErrorMessage(__('Could not get customer to unsubscribe from SMS notifications.'));

            return $resultRedirect->setPath('customer/index/index');
        }

        $customerId = (int)$this->_getSession()->getCustomerData()['customer_id'];

        $resultRedirect->setPath('customer/index/edit', ['id' => $customerId, '_current' => true]);

        try {
            /** @var \Wagento\SMSNotifications\Model\ResourceModel\SmsSubscription\Collection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());

            $collection->addFieldToFilter('customer_id', ['eq' => $customerId]);

            /** @var \Wagento\SMSNotifications\Model\SmsSubscription[] $subscribedSmsTypes */
            $subscribedSmsTypes = $collection->getItems();
            $messages = [
                'error' => [
                    'one' => 'The customer could not be unsubscribed from 1 SMS notification.',
                    'multiple' => 'The customer could not be unsubscribed from %1 SMS notifications.'
                ],
                'success' => [
                    'one' => 'The customer has been unsubscribed from 1 SMS notification.',
                    'multiple' => 'The customer has been unsubscribed from %1 SMS notifications.'
                ]
            ];

            $this->smsSubscriptionManagement->removeSubscriptions($subscribedSmsTypes, [], $customerId, $messages);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while unsubscribing the customer from the SMS notifications.')
            );
            $this->logger->critical(
                __('Could not unsubscribe customer from SMS notifications. Error: %1', $e->getMessage()),
                [
                    'customer_id' => $customerId
                ]
            );
        }

        return $resultRedirect;
    }
}
