<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Logger\Handler
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace Linkmobility\Notifications\Logger\Handler;

use Linkmobility\Notifications\Api\ConfigInterface;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base;

/**
 * Log File Handler
 *
 * @package Teamwork\Notifications\Logger\Handler
 * @author Joseph Leedy <joseph@wagento.com>
 */
class FileHandler extends Base
{
    /**
     * @var \Linkmobility\Notifications\Api\ConfigInterface
     */
    private $config;

    public function __construct(
        DriverInterface $filesystem,
        ConfigInterface $config,
        $filePath = null
    ) {
        $this->config = $config;
        $fileName = '/var/log/sms_notifications.log';

        parent::__construct($filesystem, $filePath, $fileName);
    }

    /**
     * {@inheritdoc}
     */
    public function isHandling(array $record)
    {
        return !(!$this->config->isEnabled() || !$this->config->isLoggingEnabled());
    }
}
