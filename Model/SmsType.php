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

use Linkmobility\Notifications\Api\Data\SmsTypeInterface;
use Linkmobility\Notifications\Api\Data\SmsTypeInterfaceFactory;
use Linkmobility\Notifications\Model\ResourceModel\SmsType as SmsTypeResource;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;

/**
 * SMS Type Model
 *
 * @package Linkmobility\Notifications\Model
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @author Joseph Leedy <joseph@wagento.com>
 */
class SmsType extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    protected $_eventPrefix = 'linkmobility_notifications';
    /**
     * {@inheritdoc}
     */
    protected $_eventObject = 'sms_type';
    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;
    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    private $dataObjectProcessor;
    /**
     * @var \Linkmobility\Notifications\Api\Data\SmsTypeInterfaceFactory
     */
    private $smsTypeFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        SmsTypeInterfaceFactory $smsTypeFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->smsTypeFactory = $smsTypeFactory;
    }

    public function getDataModel(): SmsTypeInterface
    {
        $smsTypeData = $this->getData();
        $smsTypeData['is_active'] = $this->getIsActive();
        /** @var \Linkmobility\Notifications\Api\Data\SmsTypeInterface $smsType */
        $smsType = $this->smsTypeFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $smsType,
            $smsTypeData,
            SmsTypeInterface::class
        );

        return $smsType;
    }

    public function updateData(SmsTypeInterface $smsType): SmsType
    {
        $smsTypeAttributes = $this->dataObjectProcessor->buildOutputDataArray($smsType, SmsTypeInterface::class);

        foreach ($smsTypeAttributes as $attributeCode => $attributeData) {
            $this->setDataUsingMethod($attributeCode, $attributeData);
        }

        $smsTypeId = $smsType->getId();

        if ($smsTypeId) {
            $this->setId($smsTypeId);
        }

        return $this;
    }

    public function setIsActive(bool $isActive): SmsType
    {
        $this->setData('is_active', (int)$isActive);
    }

    public function getIsActive(): bool
    {
        return (bool)$this->getData('is_active');
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_init(SmsTypeResource::class);
        $this->setIdFieldName('sms_type_id');
    }
}
