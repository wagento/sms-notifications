<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Plugin\Block\Form
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Plugin\Block\Form;

use LinkMobility\SMSNotifications\Api\ConfigInterface;
use Magento\Customer\Block\Form\Register;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Plug-in for {@see \Magento\Customer\Block\Form\Register::getFormData()}
 *
 * @package LinkMobility\SMSNotifications\Plugin\Block\Form
 * @author Joseph Leedy <joseph@wagento.com>
 */
class RegisterPlugin
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    /**
     * @var \LinkMobility\SMSNotifications\Api\ConfigInterface
     */
    private $config;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        ConfigInterface $config
    ) {
        $this->customerSession = $customerSession;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * Make the Customer form data available for later retrieval in our blocks
     */
    public function aroundGetFormData(Register $subject, callable $proceed)
    {
        $data = $subject->getData('form_data');

        if ($data !== null) {
            return $proceed();
        }

        try {
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
        } catch (NoSuchEntityException $e) {
            $websiteId = null;
        }

        if (!$this->config->isEnabled($websiteId) ) {
            return $proceed();
        }

        $customerFormData = $this->customerSession->getCustomerFormData();

        $result = $proceed();

        $this->customerSession->setCustomerFormData($customerFormData);

        return $result;
    }
}
