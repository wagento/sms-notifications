<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Controller\Adminhtml\Subscription
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Controller\Adminhtml\Subscription;

use LinkMobility\SMSNotifications\Model\ResourceModel\SmsSubscription\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

/**
 * Bulk Delete SMS Subscription Action
 *
 * @package LinkMobility\SMSNotifications\Controller\Adminhtml\Subscription
 * @author Joseph Leedy <joseph@wagento.com>
 */
class MassDelete extends Action
{
    const ADMIN_RESOURCE = 'LinkMobility_SMSNotifications::manage_sms_subscriptions';

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    private $filter;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \LinkMobility\SMSNotifications\Model\ResourceModel\SmsSubscription\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);

        $this->filter = $filter;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (
            $this->_getSession()->hasCustomerData()
            && array_key_exists('customer_id', $this->_getSession()->getCustomerData())
        ) {
            $customerId = (string)$this->_getSession()->getCustomerData()['customer_id'];
            $resultRedirect->setPath('customer/index/edit', ['id' => $customerId, '_current' => true]);
        } else {
            $customerId = null;
            $resultRedirect->setPath('customer/index/index');
        }

        try {
            /** @var \LinkMobility\SMSNotifications\Model\ResourceModel\SmsSubscription\Collection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $deletedSubscriptions = 0;

            $collection->addFieldToFilter('customer_id', ['eq' => $customerId]);

            /** @var \LinkMobility\SMSNotifications\Model\SmsSubscription $subscription */
            foreach ($collection->getItems() as $subscription) {
                try {
                    $subscription->getResource()->delete($subscription);
                    ++$deletedSubscriptions;
                } catch (\Exception $e) {
                    $this->logger->critical(
                        __('Could not unsubscribe customer from SMS notification. Error: %1', $e->getMessage()),
                        [
                            'sms_type' => $subscription->getSmsType(),
                            'customer_id' => $subscription->getCustomerId(),
                        ]
                    );
                }
            }

            $remainingSubscriptions = $collection->count() - $deletedSubscriptions;

            if ($remainingSubscriptions > 0 && $deletedSubscriptions > 0) {
                if ($remainingSubscriptions === 1) {
                    $errorMessage = __('The customer could not be unsubscribed from 1 SMS notification.');
                } else {
                    $errorMessage = __(
                        'The customer could not be unsubscribed from %1 SMS notifications.',
                        $remainingSubscriptions
                    );
                }

                $this->messageManager->addErrorMessage($errorMessage);
            } elseif ($remainingSubscriptions > 0) {
                $this->messageManager->addErrorMessage(
                    __('The customer could not be unsubscribed from the SMS notifications.')
                );
            }

            if ($deletedSubscriptions === 1) {
                $this->messageManager->addSuccessMessage(
                    __('The customer has been unsubscribed from 1 SMS notification.')
                );
            }

            if ($deletedSubscriptions > 1) {
                $this->messageManager->addSuccessMessage(
                    __('The customer has been unsubscribed from %1 SMS notifications.', $deletedSubscriptions)
                );
            }
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
