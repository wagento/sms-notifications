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

use Wagento\SMSNotifications\Api\ValidationRulesInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Validator\DataObject as ValidatorObject;
use Magento\Framework\Validator\DataObjectFactory as ValidatorObjectFactory;

/**
 * SMS Subscription Model Validator
 *
 * @package Wagento\SMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @phpcs:disable Magento2.PHP.FinalImplementation.FoundFinal -- This validator is not meant to be extended.
 */
final class SmsSubscriptionValidator extends Validator
{
    public function __construct(
        ValidatorObjectFactory $validatorObjectFactory,
        ValidationRulesInterface $validationRules
    ) {
        if (!$validationRules instanceof SmsSubscriptionValidationRules) {
            throw new \InvalidArgumentException(
                (string)__('Validation Rules object must be an instance of SmsSubscriptionValidationRules.')
            );
        }

        parent::__construct($validatorObjectFactory, $validationRules);
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     */
    public function validate(AbstractModel $model): void
    {
        if (!$model instanceof SmsSubscription) {
            throw new \InvalidArgumentException(
                (string)__('Model to validate must be an instance of SmsSubscription.')
            );
        }

        parent::validate($model);
    }

    /**
     * @throws \Zend_Validate_Exception
     */
    protected function addValidationRules(ValidatorObject $validator): void
    {
        $this->validationRules->addRequiredFieldRules($validator);
        $this->validationRules->addSmsTypeIsValidRule($validator);
    }
}
