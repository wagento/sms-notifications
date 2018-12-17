<?php

namespace Linkmobility\Notifications\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Create table 'sms_type'
         */
        $sms_type = $setup->getConnection()
            ->newTable($setup->getTable('sms_type'))
            ->addColumn(
                'sms_type_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'SMS type ID: successful order, canceled order, password change, ...'
            )->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'SMS identifier name'
            )->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '1'],
                'Whether SMS Type is enabled or disabled'
            )->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '10k',
                ['nullable' => true, 'default' => ''],
                'SMS type statuses'
            )->setComment("SMS types table");

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
                'sms_type_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'SMS type ID'
            )->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '1'],
                'SMS subscription status: enabled/disabled'
            )->addForeignKey(
                $setup->getFkName('sms_subscription', 'customer_id', 'customer_entity', 'entity_id'),
                'customer_id',
                $setup->getTable('customer_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName('sms_subscription', 'sms_type_id', 'sms_type', 'sms_type_id'),
                'sms_type_id',
                $setup->getTable('sms_type'),
                'sms_type_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment("SMS types table");

        $setup->getConnection()->createTable($sms_type);
        $setup->getConnection()->createTable($sms_subscription);
    }
}