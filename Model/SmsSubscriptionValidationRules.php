<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\LinkMobilitySMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\LinkMobilitySMSNotifications\Model;

use Wagento\LinkMobilitySMSNotifications\Api\ValidationRulesInterface;
use Wagento\LinkMobilitySMSNotifications\Model\Source\SmsType as SmsTypeSource;
use Magento\Framework\Validator\DataObject;
use Magento\Framework\Validator\NotEmpty;
use Magento\Framework\Validator\NotEmptyFactory;

/**
 * SMS Subscription Validation Rules
 *
 * @package Wagento\LinkMobilitySMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class SmsSubscriptionValidationRules implements ValidationRulesInterface
{
    /**
     * @var \Magento\Framework\Validator\NotEmptyFactory
     */
    private $notEmptyFactory;
    /**
     * @var \Zend_Validate_InArrayFactory
     */
    private $inArrayFactory;
    /**
     * @var \Wagento\LinkMobilitySMSNotifications\Model\Source\SmsType
     */
    private $smsType;

    public function __construct(
        NotEmptyFactory $notEmptyFactory,
        \Zend_Validate_InArrayFactory $inArrayFactory,
        SmsTypeSource $smsType
    ) {
        $this->notEmptyFactory = $notEmptyFactory;
        $this->inArrayFactory = $inArrayFactory;
        $this->smsType = $smsType;
    }

    /**
     * @throws \Zend_Validate_Exception
     */
    public function addRequiredFieldRules(DataObject $validator): DataObject
    {
        /** @var \Magento\Framework\Validator\NotEmpty $customerIdNotEmptyRule */
        $customerIdNotEmptyRule = $this->notEmptyFactory->create(['options' => NotEmpty::ALL]);
        /** @var \Magento\Framework\Validator\NotEmpty $smsTypeNotEmptyRule */
        $smsTypeNotEmptyRule = $this->notEmptyFactory->create(['options' => NotEmpty::ALL]);

        $customerIdNotEmptyRule->setMessage(__('Customer ID is required.'), NotEmpty::IS_EMPTY);
        $smsTypeNotEmptyRule->setMessage(__('SMS Type is required.'), NotEmpty::IS_EMPTY);

        $validator->addRule($customerIdNotEmptyRule, 'customer_id');
        $validator->addRule($smsTypeNotEmptyRule, 'sms_type');

        return $validator;
    }

    /**
     * @throws \Zend_Validate_Exception
     */
    public function addSmsTypeIsValidRule(DataObject $validator): DataObject
    {
        /** @var \Zend_Validate_InArray $smsTypeIsValidRule */
        $smsTypeIsValidRule = $this->inArrayFactory->create([
            'options' => [
                'haystack' => array_column($this->smsType->toArray(), 'code'),
                'strict' => true
            ]
        ]);

        $smsTypeIsValidRule->setMessage(__('"%value%" is not a valid type of SMS notification.'));

        $validator->addRule($smsTypeIsValidRule, 'sms_type');

        return $validator;
    }
}
