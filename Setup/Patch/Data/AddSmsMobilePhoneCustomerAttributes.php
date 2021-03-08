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

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Wagento\SMSNotifications\Model\Customer\Attribute\Source\TelephonePrefix;

/**
 * SMS Mobile Telephone Customer Attributes Installer
 *
 * @package Wagento\SMSNotifications\Setup\Patch\Data
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @codeCoverageIgnore
 */
class AddSmsMobilePhoneCustomerAttributes implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $setup;
    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    public function __construct(ModuleDataSetupInterface $setup, CustomerSetupFactory $customerSetupFactory)
    {
        $this->setup = $setup;
        $this->customerSetupFactory = $customerSetupFactory;
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
     * @throws \Exception
     */
    public function apply(): self
    {
        $this->createAttributes();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function revert(): void
    {
        $this->removeAttributes();
    }

    /**
     * @throws \Exception
     */
    private function createAttributes(): void
    {
        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->setup]);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'sms_mobile_phone_prefix',
            [
                'type' => 'varchar',
                'label' => 'Mobile Phone Prefix (for SMS)',
                'input' => 'select',
                'source' => TelephonePrefix::class,
                'required' => false,
                'visible' => true,
                'system' => false,
                'user_defined' => false,
                'visible_on_front' => true,
                'position' => 1000,
            ]
        );
        $customerSetup->addAttribute(
            Customer::ENTITY,
            'sms_mobile_phone_number',
            [
                'type' => 'varchar',
                'label' => 'Mobile Phone Number (for SMS)',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'system' => false,
                'user_defined' => false,
                'visible_on_front' => true,
                'position' => 1001,
            ]
        );

        $mobilePhonePrefixAttribute = $customerSetup->getEavConfig()->getAttribute(
            Customer::ENTITY,
            'sms_mobile_phone_prefix'
        );
        $mobilePhoneNumberAttribute = $customerSetup->getEavConfig()->getAttribute(
            Customer::ENTITY,
            'sms_mobile_phone_number'
        );
        $attributeData = [
            'attribute_set_id' => $customerSetup->getDefaultAttributeSetId(Customer::ENTITY),
            'attribute_group_id' => $customerSetup->getDefaultAttributeGroupId(Customer::ENTITY),
            'used_in_forms' => ['adminhtml_customer', 'customer_account_create'],
        ];

        $mobilePhonePrefixAttribute->addData($attributeData);
        $mobilePhonePrefixAttribute->save();

        $mobilePhoneNumberAttribute->addData($attributeData);
        $mobilePhoneNumberAttribute->save();
    }

    private function removeAttributes(): void
    {
        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->setup]);

        $customerSetup->removeAttribute(Customer::ENTITY, 'sms_mobile_phone_prefix');
        $customerSetup->removeAttribute(Customer::ENTITY, 'sms_mobile_phone_number');
    }
}
