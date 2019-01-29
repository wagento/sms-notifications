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

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Database Data Installer
 *
 * @package Linkmobility\Notifications\Setup
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;
    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    private $attributeRepository;

    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createAttributes($setup);

        $setup->endSetup();
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function createAttributes(ModuleDataSetupInterface $setup): void
    {
        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'sms_mobile_phone_number',
            [
                'type' => 'varchar',
                'label' => 'Mobile Phone Number for SMS',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'system' => false,
                'user_defined' => false,
                'visible_on_front' => true,
                'position' => 1000,
            ]
        );

        $mobilePhoneNumberAttribute = $customerSetup->getEavConfig()->getAttribute(
            Customer::ENTITY,
            'sms_mobile_phone_number'
        );

        $mobilePhoneNumberAttribute->addData([
            'attribute_set_id' => $customerSetup->getDefaultAttributeSetId(Customer::ENTITY),
            'attribute_group_id' => $customerSetup->getDefaultAttributeGroupId(Customer::ENTITY),
            'used_in_forms' => ['adminhtml_customer', 'customer_account_create', 'customer_account_edit'],
        ]);

        $this->attributeRepository->save($mobilePhoneNumberAttribute);
    }
}
