<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Model;

use Wagento\SMSNotifications\Api\Data\SmsSubscriptionInterface;
use Wagento\SMSNotifications\Api\Data\SmsSubscriptionInterfaceFactory;
use Wagento\SMSNotifications\Api\ValidatorInterface;
use Wagento\SMSNotifications\Model\ResourceModel\SmsSubscription as SmsSubscriptionResource;
use Wagento\SMSNotifications\Traits\DataObjectMagicMethods;
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
 * @package Wagento\SMSNotifications\Model
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @author Joseph Leedy <joseph@wagento.com>
 * @method $this setSmsSubscriptionId(int $smsSubscriptionId)
 * @method int getSmsSubscriptionId()
 * @method $this setCustomerId(int $customerId)
 * @method int getCustomerId()
 * @method $this setSmsType(string $smsType)
 * @method string getSmsType()
 */
class SmsSubscription extends AbstractModel
{
    use DataObjectMagicMethods;

    /**
     * {@inheritdoc}
     */
    protected $_eventPrefix = 'sms_notifications';
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
     * @var \Wagento\SMSNotifications\Api\Data\SmsSubscriptionInterfaceFactory
     */
    private $smsSubscriptionFactory;
    /**
     * @var \Wagento\SMSNotifications\Api\ValidatorInterface
     */
    private $validator;

    public function __construct(
        Context $context,
        Registry $registry,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        SmsSubscriptionInterfaceFactory $smsSubscriptionFactory,
        ValidatorInterface $validator,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        if (!$validator instanceof SmsSubscriptionValidator) {
            throw new \InvalidArgumentException(
                (string)__('Validator must be an instance of SmsSubscriptionValidator.')
            );
        }

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->smsSubscriptionFactory = $smsSubscriptionFactory;
        $this->validator = $validator;
    }

    public function getDataModel(): SmsSubscriptionInterface
    {
        $smsSubscriptionData = $this->getData();
        /** @var \Wagento\SMSNotifications\Api\Data\SmsSubscriptionInterface $smsSubscription */
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

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_init(SmsSubscriptionResource::class);
        $this->setIdFieldName('sms_subscription_id');
    }

    /**
     * {@inheritdoc}
     * @throws \Zend_Validate_Exception
     */
    protected function _getValidationRulesBeforeSave(): \Zend_Validate_Interface
    {
        return $this->validator->getValidator();
    }
}
