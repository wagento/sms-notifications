<?php
namespace Linkmobility\Notifications\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $data = [
            ['name' => 'All', 'description' => 'All notifications'],
            ['name' => 'Successful order', 'description' => 'Enables successful order creation notification, triggered when your order is created.'],
            ['name' => 'Updated order', 'description' => 'Enables updated order notification, triggered when your order has some status change that is not listed below.'],
            ['name' => 'Shipped order item(s)', 'description' => 'Enables shipped order items notification, triggered when one or more items in your order were shipped to the specified shipping address.'],
            ['name' => 'Refunded order item(s)', 'description' => 'Enables refunded order items notification, triggered when one or more items in your order were refunded.'],
            ['name' => 'Completed order', 'description' => 'Enables completed order notification, triggered when your order is fulfilled.'],
            ['name' => 'On-hold status change', 'description' => 'Enables on-hold status change notification, triggered when your order has any problem and store can\'t fulfill.']
        ];
        foreach ($data as $row) {
            $setup->getConnection()
                ->insertForce($setup->getTable('sms_type_id'), $row);
        }
    }
}