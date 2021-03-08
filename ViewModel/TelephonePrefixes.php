<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\ViewModel
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 *
 * @phpcs:disable Generic.Files.LineLength.TooLong
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\ViewModel;

use Wagento\SMSNotifications\Model\ResourceModel\TelephonePrefix\CollectionFactory as TelephonePrefixCollectionFactory;
use Magento\Customer\Model\Customer;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Telephone Prefixes View Model
 *
 * @package Wagento\SMSNotifications\ViewModel
 * @author Joseph Leedy <joseph@wagento.com>
 */
class TelephonePrefixes implements ArgumentInterface
{
    /**
     * @var \Magento\Directory\Helper\Data
     */
    private $directoryHelper;
    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    private $attributeRepository;
    /**
     * @var \Wagento\SMSNotifications\Model\ResourceModel\TelephonePrefix\CollectionFactory
     */
    private $telephonePrefixCollectionFactory;

    public function __construct(
        DirectoryHelper $directoryHelper,
        AttributeRepositoryInterface $attributeRepository,
        TelephonePrefixCollectionFactory $telephonePrefixCollectionFactory
    ) {
        $this->directoryHelper = $directoryHelper;
        $this->attributeRepository = $attributeRepository;
        $this->telephonePrefixCollectionFactory = $telephonePrefixCollectionFactory;
    }

    public function getOptions(): array
    {
        try {
            $options = $this->attributeRepository->get(Customer::ENTITY, 'sms_mobile_phone_prefix')
                ->getSource()
                ->getAllOptions(false);
        } catch (NoSuchEntityException | LocalizedException $e) {
            $options = [];
        }

        return $options;
    }

    public function getDefaultPrefix(): string
    {
        /** @var \Wagento\SMSNotifications\Model\TelephonePrefix $prefix */
        $prefix = $this->telephonePrefixCollectionFactory->create()
            ->addFieldToFilter('country_code', ['eq' => $this->directoryHelper->getDefaultCountry()])
            ->setPageSize(1)
            ->getFirstItem();

        if ($prefix->getId() === null) {
            return '';
        }

        return $prefix->getCountryCode() . '_' . $prefix->getPrefix();
    }
}
