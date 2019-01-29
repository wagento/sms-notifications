<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Model;

use Linkmobility\Notifications\Api\Data\TelephonePrefixInterface;
use Linkmobility\Notifications\Api\Data\TelephonePrefixInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Linkmobility\Notifications\Model\ResourceModel\TelephonePrefix as TelephonePrefixResource;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;

/**
 * Telephone Prefix Data Model
 *
 * @package Linkmobility\Notifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @method $this setCountryCode(string $countryCode)
 * @method string getCountryCode()
 * @method bool hasCountryCode()
 * @method $this setPrefix(int $prefix)
 * @method int getPrefix()
 * @method bool hasPrefix()
 */
class TelephonePrefix extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    protected $_eventPrefix = 'linkmobility_notifications_telephone_prefix';
    /**
     * {@inheritdoc}
     */
    protected $_eventObject = ' telephone_prefix';
    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;
    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    private $dataObjectProcessor;
    /**
     * @var \Linkmobility\Notifications\Api\Data\TelephonePrefixInterfaceFactory
     */
    private $telephonePrefixFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        TelephonePrefixInterfaceFactory $telephonePrefixFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->telephonePrefixFactory = $telephonePrefixFactory;
    }

    public function getDataModel(): TelephonePrefixInterface
    {
        $telephonePrefixData = $this->getData();
        /** @var \Linkmobility\Notifications\Api\Data\TelephonePrefixInterface $telephonePrefix */
        $telephonePrefix = $this->telephonePrefixFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $telephonePrefix,
            $telephonePrefixData,
            TelephonePrefixInterface::class
        );

        return $telephonePrefix;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(TelephonePrefixResource::class);
        $this->setIdFieldName('country_code');
    }
}
