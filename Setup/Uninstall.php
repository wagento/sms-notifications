<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\LinkMobilitySMSNotifications\Setup
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\LinkMobilitySMSNotifications\Setup;

use Magento\Customer\Model\Customer;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

/**
 * Database Data & Schema Remover
 *
 * @package Wagento\LinkMobilitySMSNotifications\Setup
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @codeCoverageIgnore
 */
class Uninstall implements UninstallInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->removeSmsSubscriptionTable($setup);
        $this->removeTelephonePrefixDirectoryTable($setup);
        $this->removeMobileTelephoneNumberAttributes();
        $this->removeSettings($setup);
    }

    private function removeSmsSubscriptionTable(SchemaSetupInterface $setup): void
    {
        $setup->getConnection()->dropTable($setup->getTable('sms_subscription'));
    }

    private function removeTelephonePrefixDirectoryTable(SchemaSetupInterface $setup): void
    {
        $setup->getConnection()->dropTable($setup->getTable('directory_telephone_prefix'));
    }

    private function removeMobileTelephoneNumberAttributes(): void
    {
        $eavSetup = $this->eavSetupFactory->create();

        $eavSetup->removeAttribute(Customer::ENTITY, 'sms_mobile_phone_prefix');
        $eavSetup->removeAttribute(Customer::ENTITY, 'sms_mobile_phone_number');
    }

    private function removeSettings(SchemaSetupInterface $setup): void
    {
        $setup->getConnection()->delete(
            $setup->getTable('core_config_data'),
            '`path` LIKE \'sms_notifications/%\''
        );
    }
}
