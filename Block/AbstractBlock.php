<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Block
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Block;

use LinkMobility\SMSNotifications\Api\ConfigInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;

/**
 * SMS Notifications Base Block
 *
 * @package LinkMobility\SMSNotifications\Block
 * @author Joseph Leedy <joseph@wagento.com>
 */
abstract class AbstractBlock extends Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \LinkMobility\SMSNotifications\Api\ConfigInterface
     */
    private $config;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        ConfigInterface $config,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->customerSession = $customerSession;
        $this->config = $config;
    }

    public function getFormData(): DataObject
    {
        $data = $this->getData('form_data');

        if ($data === null) {
            $formData = $this->customerSession->getCustomerFormData(true);
            $data = new DataObject();

            if ($formData) {
                $data->addData($formData);
                $data->setCustomerData(1);
            }

            $this->setData('form_data', $data);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        try {
            $websiteId = (string)$this->_storeManager->getStore()->getWebsiteId();
        } catch (NoSuchEntityException $e) {
            $websiteId = null;
        }

        if (!$this->config->isEnabled($websiteId)) {
            return '';
        }

        return parent::toHtml();
    }
}
