<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Model\ResourceModel
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Model\ResourceModel;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * SMS Subscription Resource Model
 *
 * @package LinkMobility\SMSNotifications\Model\ResourceModel
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @author Joseph Leedy <joseph@wagento.com>
 */
class SmsSubscription extends AbstractDb
{
    const TABLE_NAME = 'sms_subscription';
    const IDENTITY_COLUMN = 'sms_subscription_id';

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $dataObjectFactory;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        DataObjectFactory $dataObjectFactory,
        ?string $connectionName = null
    ) {
        parent::__construct($context, $connectionName);

        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::IDENTITY_COLUMN);
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _checkUnique(AbstractModel $object)
    {
        $data = $this->dataObjectFactory->create(['data' => $this->_prepareDataForSave($object)]);
        $smsType = $data->getData('sms_type');
        $customerId = $data->getData('customer_id');
        $select = $this->getConnection()->select()->from($this->getMainTable());

        $select->reset(Select::WHERE);
        $select->where('sms_type=?', $smsType);
        $select->where('customer_id=?', $customerId);

        $smsSubscriptions = $this->getConnection()->fetchRow($select);

        if (!empty($smsSubscriptions)) {
            throw new AlreadyExistsException(
                __('Customer with ID %1 is already subscribed to "%2" SMS notification.', $customerId, $smsType)
            );
        }

        return $this;
    }
}
