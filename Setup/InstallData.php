<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Setup
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Setup;

use LinkMobility\SMSNotifications\Model\Customer\Attribute\Source\TelephonePrefix;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Psr\Log\LoggerInterface;

/**
 * Database Data Installer
 *
 * @package LinkMobility\SMSNotifications\Setup
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
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        LoggerInterface $logger
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createAttributes($setup);
        $this->importCountryPhonePrefixes($setup);

        $setup->endSetup();
    }

    /**
     * @throws \Exception
     */
    private function createAttributes(ModuleDataSetupInterface $setup): void
    {
        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

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

    /**
     * @phpcs:disable Generic.Files.LineLength.TooLong
     */
    private function importCountryPhonePrefixes(ModuleDataSetupInterface $setup): void
    {
        $countryPrefixes = file_get_contents(__DIR__ . '/_data/country_telephone_prefixes.json');

        if ($countryPrefixes === false) {
            $this->logger->critical(__('Could not get JSON file of country telephone prefixes to import.'));

            return;
        }

        $countryPrefixes = json_decode($countryPrefixes, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->critical(
                __('Could not parse file containing country telephone prefixes as JSON. Error: "%1".', json_last_error_msg())
            );

            return;
        }

        // Strip header row
        unset($countryPrefixes[0]);

        $countryPrefixesTable = $setup->getTable('directory_telephone_prefix');

        foreach ($countryPrefixes as $countryPrefix) {
            $setup->getConnection()->insert($countryPrefixesTable, $countryPrefix);
        }
    }
}
