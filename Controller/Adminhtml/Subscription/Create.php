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
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

/**
 * Create SMS Subscription Action
 *
 * @package Wagento\SMSNotifications\Controller\Adminhtml\Subscription
 * @author Joseph Leedy <joseph@wagento.com>
 */
class Create extends Action
{
    const ADMIN_RESOURCE = 'Wagento_SMSNotifications::manage_sms_subscriptions';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;
    /**
     * @var \Wagento\SMSNotifications\Api\SmsSubscriptionManagementInterface
     */
    private $smsSubscriptionManagement;

    public function __construct(
        Context $context,
        SmsSubscriptionManagementInterface $smsSubscriptionManagement
    ) {
        parent::__construct($context);

        $this->_request = $context->getRequest();
        $this->smsSubscriptionManagement = $smsSubscriptionManagement;
    }

    /**
     * {@inheritdoc}
     *
     * @phpcs:disable Generic.Files.LineLength.TooLong
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (
            !$this->_getSession()->hasCustomerData()
            || !array_key_exists('customer_id', $this->_getSession()->getCustomerData())
        ) {
            $this->messageManager->addErrorMessage(__('Could not get customer to subscribe to SMS notification.'));
            $resultRedirect->setPath('customer/index/index');

            return $resultRedirect;
        }

        $customerId = (int)$this->_getSession()->getCustomerData()['customer_id'];
        $smsType = $this->_request->getParam('sms_type');

        $resultRedirect->setPath('customer/index/edit', ['id' => $customerId, '_current' => true]);

        if (empty($smsType)) {
            $this->messageManager->addErrorMessage(__('Please select an SMS notification to subscribe the customer to.'));

            return $resultRedirect;
        }

        if ($this->smsSubscriptionManagement->createSubscription($smsType, $customerId)) {
            $this->messageManager->addSuccessMessage(
                __('The customer has been subscribed to the SMS notification.')
            );
        } else {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while subscribing the customer to the SMS notification.')
            );
        }

        return $resultRedirect;
    }
}
