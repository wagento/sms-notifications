<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Model;

use LinkMobility\SMSNotifications\Api\SmsSubscriptionValidatorInterface;
use Magento\Framework\Validator\DataObject;
use Magento\Framework\Validator\DataObjectFactory as ValidatorObjectFactory;

/**
 * SMS Subscription Validator
 *
 * @package LinkMobility\SMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class SmsSubscriptionValidator implements SmsSubscriptionValidatorInterface
{
    /**
     * @var \Magento\Framework\Validator\DataObjectFactory
     */
    private $validatorObjectFactory;
    /**
     * @var \LinkMobility\SMSNotifications\Model\SmsSubscriptionValidationRules
     */
    private $validationRules;
    private $isValid = true;
    /**
     * @var string[]
     */
    private $messages = [];

    public function __construct(
        ValidatorObjectFactory $validatorObjectFactory,
        SmsSubscriptionValidationRules $validationRules
    ) {
        $this->validatorObjectFactory = $validatorObjectFactory;
        $this->validationRules = $validationRules;
    }

    /**
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     */
    public function validate(SmsSubscription $smsSubscription): void
    {
        /** @var \Magento\Framework\Validator\DataObject $validator */
        $validator = $this->getValidator();

        if (!$validator->isValid($smsSubscription)) {
            $this->messages = $validator->getMessages();
            $this->isValid = false;
        }
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @throws \Zend_Validate_Exception
     */
    public function getValidator(): \Zend_Validate_Interface
    {
        /** @var \Magento\Framework\Validator\DataObject $validator */
        $validator = $this->validatorObjectFactory->create();

        $this->addValidationRules($validator);

        return $validator;
    }

    /**
     * @throws \Zend_Validate_Exception
     */
    private function addValidationRules(DataObject $validator): void
    {
        $this->validationRules->addRequiredFieldRules($validator);
        $this->validationRules->addSmsTypeIsValidRule($validator);
    }
}
