<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Block\Customer\Account\Create
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Block\Customer\Account\Create;

use Linkmobility\Notifications\Api\ConfigInterface;
use Linkmobility\Notifications\Block\AbstractBlock;
use Linkmobility\Notifications\Model\ResourceModel\TelephonePrefix\CollectionFactory as TelephonePrefixCollectionFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\View\Element\Template\Context;

/**
 * SMS Notifications Mobile Telephone Block
 *
 * @package Linkmobility\Notifications\Block\Customer\Account\Create
 * @author Joseph Leedy <joseph@wagento.com>
 */
class MobileTelephoneField extends AbstractBlock
{
    /**
     * @var \Magento\Directory\Helper\Data
     */
    private $directoryHelper;
    /**
     * @var \Linkmobility\Notifications\Model\ResourceModel\TelephonePrefix\CollectionFactory
     */
    private $telephonePrefixCollectionFactory;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        ConfigInterface $config,
        DirectoryHelper $directoryHelper,
        TelephonePrefixCollectionFactory $telephonePrefixCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $config, $data);

        $this->directoryHelper = $directoryHelper;
        $this->telephonePrefixCollectionFactory = $telephonePrefixCollectionFactory;
    }

    public function getFieldVisibility(): string
    {
        return $this->getFullMobileNumber() !== '' && $this->getTelephonePrefix() !== null
            && $this->getTelephonePrefix() !== '' ? 'true' : 'false';
    }

    public function getTelephonePrefix(): ?string
    {
        $telephonePrefix = $this->getFormData()->getMobileTelephonePrefix();

        if ($telephonePrefix === null) {
            /** @var \Linkmobility\Notifications\Model\TelephonePrefix $prefix */
            $prefix = $this->telephonePrefixCollectionFactory->create()
                ->addFieldToFilter('country_code', ['eq' => $this->directoryHelper->getDefaultCountry()])
                ->setPageSize(1)
                ->getFirstItem();
            $telephonePrefix = $prefix->getCountryCode() . '_' . $prefix->getPrefix();
        }

        return $telephonePrefix;
    }

    public function getTelephoneNumber(): string
    {
        return $this->getFormData()->getMobileTelephoneNumber() ?? '';
    }

    public function getFullMobileNumber(): string
    {
        return $this->getFormData()->getSmsMobilePhoneNumber() ?? '';
    }
}
