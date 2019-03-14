<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Observer
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Observer;

use LinkMobility\SMSNotifications\Api\ConfigInterface;
use LinkMobility\SMSNotifications\Api\SmsSubscriptionManagementInterface;
use LinkMobility\SMSNotifications\Model\SmsSender;
use LinkMobility\SMSNotifications\Model\Source\SmsType as SmsTypeSource;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Observer for customer_register_success event
 *
 * @package LinkMobility\SMSNotifications\Observer
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
     * @var \LinkMobility\SMSNotifications\Api\ConfigInterface
     */
    private $config;
    /**
     * @var \LinkMobility\SMSNotifications\Model\Source\SmsType
     */
    private $smsTypeSource;
    /**
     * @var \LinkMobility\SMSNotifications\Api\SmsSubscriptionManagementInterface
     */
    private $smsSubscriptionManagement;
    /**
     * @var \LinkMobility\SMSNotifications\Model\SmsSender\WelcomeSender
     */
    private $smsSender;

    public function __construct(
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        ConfigInterface $config,
        SmsTypeSource $smsTypeSource,
        SmsSubscriptionManagementInterface $smsSubscriptionManagement,
        SmsSender $smsSender
    ) {
        $this->storeManager = $storeManager;
        $this->request = $request;
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
            $this->smsSender->send($customer);
        }
    }
}
