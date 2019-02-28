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

use LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterfaceFactory;
use LinkMobility\SMSNotifications\Api\SmsSubscriptionRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;

/**
 * Bulk Create SMS Subscription Action
 *
 * @package LinkMobility\SMSNotifications\Controller\Adminhtml\Subscription
 * @author Joseph Leedy <joseph@wagento.com>
 */
class MassCreate extends Action
{
    const ADMIN_RESOURCE = 'LinkMobility_SMSNotifications::manage_sms_subscriptions';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterfaceFactory
     */
    private $smsSubscriptionFactory;
    /**
     * @var \LinkMobility\SMSNotifications\Api\SmsSubscriptionRepositoryInterface
     */
    private $smsSubscriptionRepository;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        SmsSubscriptionInterfaceFactory $smsSubscriptionFactory,
        SmsSubscriptionRepositoryInterface $smsSubscriptionRepository
    ) {
        parent::__construct($context);

        $this->logger = $logger;
        $this->smsSubscriptionFactory = $smsSubscriptionFactory;
        $this->smsSubscriptionRepository = $smsSubscriptionRepository;
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
            $this->messageManager->addErrorMessage(__('Could not get customer to subscribe SMS notification to.'));
            $resultRedirect->setPath('customer/index/index');

            return $resultRedirect;
        }

        $customerId = (int)$this->_getSession()->getCustomerData()['customer_id'];
        $selectedSmsTypes = $this->getRequest()->getParam('selected', []);
        $createdSubscriptions = 0;

        $resultRedirect->setPath('customer/index/edit', ['id' => $customerId, '_current' => true]);

        if (count($selectedSmsTypes) === 0) {
            $this->messageManager->addErrorMessage(__('Please select an SMS notification to subscribe the customer to.'));

            return $resultRedirect;
        }

        try {
            foreach ($selectedSmsTypes as $smsType) {
                try {
                    /** @var \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface $subscription */
                    $subscription = $this->smsSubscriptionFactory->create();

                    $subscription->setSmsType($smsType);
                    $subscription->setCustomerId($customerId);

                    $this->smsSubscriptionRepository->save($subscription);

                    ++$createdSubscriptions;
                } catch (CouldNotSaveException $e) {
                    $this->logger->critical(
                        __('Could not subscribe customer to SMS notification. Error: %1', $e->getMessage()),
                        [
                            'sms_type' => $smsType,
                            'customer_id' => $customerId
                        ]
                    );
                }
            }

            $remainingSubscriptions = count($selectedSmsTypes) - $createdSubscriptions;

            if ($remainingSubscriptions === 1) {
                $this->messageManager->addErrorMessage(
                    __('The customer could not be subscribed to 1 SMS notification.')
                );
            }

            if ($remainingSubscriptions > 1) {
                $this->messageManager->addErrorMessage(
                    __('The customer could not be subscribed to %1 SMS notifications.', $remainingSubscriptions)
                );
            }

            if ($createdSubscriptions === 1) {
                $this->messageManager->addSuccessMessage(__('The customer has been subscribed to 1 SMS notification.'));
            }

            if ($createdSubscriptions > 1) {
                $this->messageManager->addSuccessMessage(
                    __('The customer has been subscribed to %1 SMS notifications.', $createdSubscriptions)
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while subscribing the customer to the SMS notifications.')
            );
            $this->logger->critical(
                __('Could not subscribe customer to SMS notifications. Error: %1', $e->getMessage()),
                [
                    'customer_id' => $customerId
                ]
            );
        }

        return $resultRedirect;
    }
}
