<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Logger\Handler
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Logger\Handler;

use Wagento\SMSNotifications\Api\ConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Log File Handler
 *
 * @package Wagento\SMSNotifications\Logger\Handler
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @phpcs:disable Magento2.PHP.FinalImplementation.FoundFinal -- This log handler is not meant to be extended.
 */
final class FileHandler extends Base
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Wagento\SMSNotifications\Api\ConfigInterface
     */
    private $config;

    public function __construct(
        DriverInterface $filesystem,
        StoreManagerInterface $storeManager,
        ConfigInterface $config,
        $filePath = null
    ) {
        $this->config = $config;
        $fileName = '/var/log/sms_notifications.log';

        parent::__construct($filesystem, $filePath, $fileName);

        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function isHandling(array $record)
    {
        try {
            $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        } catch (NoSuchEntityException $e) {
            $websiteId = null;
        }

        return !(!$this->config->isEnabled($websiteId) || !$this->config->isLoggingEnabled());
    }
}
