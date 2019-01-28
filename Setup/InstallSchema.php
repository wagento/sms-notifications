<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Setup
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Database Schema Installer
 *
 * @package Linkmobility\Notifications\Setup
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $sms_subscription = $setup->getConnection()
            ->newTable($setup->getTable('sms_subscription'))
            ->addColumn(
                'sms_subscription_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'SMS subscription ID'
            )->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Customer ID'
            )->addColumn(
                'sms_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'SMS type code (e.g. "order_placed")'
            )->addForeignKey(
                $setup->getFkName('sms_subscription', 'customer_id', 'customer_entity', 'entity_id'),
                'customer_id',
                $setup->getTable('customer_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment("SMS types table");

        $setup->getConnection()->createTable($sms_subscription);
    }
}