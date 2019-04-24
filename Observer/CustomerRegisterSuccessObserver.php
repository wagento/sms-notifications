<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Observer
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Observer;

use Wagento\SMSNotifications\Api\ConfigInterface;
use Wagento\SMSNotifications\Api\SmsSubscriptionManagementInterface;
use Wagento\SMSNotifications\Model\SmsSender;
use Wagento\SMSNotifications\Model\Source\SmsType as SmsTypeSource;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Observer for customer_register_success event
 *
 * @package Wagento\SMSNotifications\Observer
 * @author Joseph Leedy <joseph@wagento.com>
 * @see \Magento\Customer\Controller\Account\CreatePost::execute()
 */
class CustomerRegisterSuccessObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;
    /**
     * @var \Wagento\SMSNotifications\Api\ConfigInterface
     */
    private $config;
    /**
     * @var \Wagento\SMSNotifications\Model\Source\SmsType
     */
    private $smsTypeSource;
    /**
     * @var \Wagento\SMSNotifications\Api\SmsSubscriptionManagementInterface
     */
    private $smsSubscriptionManagement;
    /**
     * @var \Wagento\SMSNotifications\Model\SmsSender\WelcomeSender
     */
    private $smsSender;

    public function __construct(
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        CustomerFactory $customerFactory,
        ConfigInterface $config,
        SmsTypeSource $smsTypeSource,
        SmsSubscriptionManagementInterface $smsSubscriptionManagement,
        SmsSender $smsSender
    ) {
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->customerFactory = $customerFactory;
        $this->config = $config;
        $this->smsTypeSource = $smsTypeSource;
        $this->smsSubscriptionManagement = $smsSubscriptionManagement;
        $this->smsSender = $smsSender;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $observer->getData('customer');
        $smsNotificationsParameters = $this->request->getParam('sms_notifications', []);

        try {
            $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        } catch (NoSuchEntityException $e) {
            $websiteId = null;
        }

        if (
            !$this->config->isEnabled($websiteId)
            || !$this->request->isPost()
            || empty($smsNotificationsParameters)
            || empty($smsNotificationsParameters['subscribed'])
        ) {
            return;
        }

        if (
            array_key_exists('sms_types', $smsNotificationsParameters)
            && trim($smsNotificationsParameters['sms_types']) !== ''
        ) {
            $smsTypes = explode(',', $smsNotificationsParameters['sms_types']);
        } else {
            $smsTypes = array_column($this->smsTypeSource->toArray(), 'code');
        }

        $createdSmsSubscriptions = $this->smsSubscriptionManagement->createSubscriptions(
            $smsTypes,
            (int)$customer->getId()
        );

        if ($createdSmsSubscriptions > 0) {
            $this->sendWelcomeMessage($customer);
        }
    }

    private function sendWelcomeMessage(CustomerInterface $customerData): void
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->customerFactory->create()->updateData($customerData);

        $this->smsSender->send($customer);
    }
}
