<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\ViewModel
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\ViewModel;

use Wagento\SMSNotifications\Api\ConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Terms & Conditions View Model
 *
 * @package Wagento\SMSNotifications\ViewModel
 * @author Joseph Leedy <joseph@wagento.com>
 */
class TermsConditions implements ArgumentInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Wagento\SMSNotifications\Api\ConfigInterface
     */
    private $config;

    public function __construct(StoreManagerInterface $storeManager, ConfigInterface $config)
    {
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    public function getContent(): string
    {
        try {
            $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        } catch (NoSuchEntityException $e) {
            $websiteId = null;
        }

        return $this->config->getTermsAndConditions($websiteId) ?? '';
    }
}
