<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Setup\Patch\Data
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

/**
 * SMS Notification Settings Uninstaller
 *
 * @package Wagento\SMSNotifications\Setup\Patch\Data
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @codeCoverageIgnore
 */
class RemoveConfigData implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $setup;

    public function __construct(ModuleDataSetupInterface $setup)
    {
        $this->setup = $setup;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function apply(): self
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function revert(): void
    {
        $this->setup->getConnection()->delete(
            $this->setup->getTable('core_config_data'),
            '`path` LIKE \'sms_notifications/%\''
        );
    }
}
