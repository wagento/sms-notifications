<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Block
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Block;

use Wagento\SMSNotifications\Api\ConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;

/**
 * SMS Notifications Base Block
 *
 * @package Wagento\SMSNotifications\Block
 * @author Joseph Leedy <joseph@wagento.com>
 */
abstract class AbstractBlock extends Template
{
    /**
     * @var \Wagento\SMSNotifications\Api\ConfigInterface
     */
    protected $config;

    public function __construct(
        Context $context,
        ConfigInterface $config,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        if (!$this->config->isEnabled($this->getWebsiteId())) {
            return '';
        }

        return parent::toHtml();
    }

    protected function getWebsiteId(): ?int
    {
        try {
            $websiteId = (int)$this->_storeManager->getStore()->getWebsiteId();
        } catch (NoSuchEntityException $e) {
            $websiteId = null;
        }

        return $websiteId;
    }
}
