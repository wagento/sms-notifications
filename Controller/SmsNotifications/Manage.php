<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Controller\SmsNotifications
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Controller\SmsNotifications;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\StoreManagerInterface;
use Wagento\SMSNotifications\Model\Config;

/**
 * Manage SMS Subscriptions Controller
 *
 * @package Wagento\SMSNotifications\Controller\SmsNotifications
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @author Joseph Leedy <joseph@wagento.com>
 */
class Manage extends AbstractAccount
{
    /**
     * @var \Magento\Store\Api\StoreManagementInterface
     */
    private $storeManager;
    /**
     * @var \Wagento\SMSNotifications\Model\Config
     */
    private $config;

    public function __construct(Context $context, StoreManagerInterface $storeManager, Config $config)
    {
        parent::__construct($context);

        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        } catch (NoSuchEntityException $e) {
            $websiteId = null;
        }

        if (!$this->config->isEnabled($websiteId)) {
            throw new NotFoundException(__('SMS Notifications is not enabled.'));
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
