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

use Linkmobility\Notifications\Api\Data\SmsSubscriptionInterface;
use Linkmobility\Notifications\Api\Data\SmsSubscriptionInterfaceFactory;
use Linkmobility\Notifications\Model\ResourceModel\SmsSubscription as SmsSubscriptionResource;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;

/**
 * SMS Subscription Model
 *
 * @package Linkmobility\Notifications\Model
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @author Joseph Leedy <joseph@wagento.com>
 * @method int getSmsSubscriptionId()
 * @method string getCustomerId()
 * @method int getSmsTypeId()
 */
class SmsSubscription extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    protected $_eventPrefix = 'linkmobility_notifications';
    /**
     * {@inheritdoc}
     */
    protected $_eventObject = 'sms_subscription';
    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;
    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    private $dataObjectProcessor;
    /**
     * @var \Linkmobility\Notifications\Api\Data\SmsSubscriptionInterfaceFactory
     */
    private $smsSubscriptionFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        SmsSubscriptionInterfaceFactory $smsSubscriptionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->smsSubscriptionFactory = $smsSubscriptionFactory;
    }

    public function getDataModel(): SmsSubscriptionInterface
    {
        $smsSubscriptionData = $this->getData();
        $smsSubscriptionData['is_active'] = (int)$this->getIsActive();
        /** @var \Linkmobility\Notifications\Api\Data\SmsSubscriptionInterface $smsSubscription */
        $smsSubscription = $this->smsSubscriptionFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $smsSubscription,
            $smsSubscriptionData,
            SmsSubscriptionInterface::class
        );

        return $smsSubscription;
    }

    public function updateData(SmsSubscriptionInterface $smsSubscription): SmsSubscription
    {
        $smsSubscriptionAttributes = $this->dataObjectProcessor->buildOutputDataArray(
            $smsSubscription,
            SmsSubscriptionInterface::class
        );

        foreach ($smsSubscriptionAttributes as $attributeCode => $attributeData) {
            $this->setDataUsingMethod($attributeCode, $attributeData);
        }

        $smsSubscriptionId = $smsSubscription->getId();

        if ($smsSubscriptionId) {
            $this->setId($smsSubscriptionId);
        }

        return $this;
    }

    public function setIsActive(bool $isActive): SmsSubscription
    {
        return $this->setData('is_active', (int)$isActive);
    }

    public function getIsActive(): bool
    {
        return (bool)$this->getData('is_active');
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_init(SmsSubscriptionResource::class);
        $this->setIdFieldName('sms_subscription_id');
    }
}
