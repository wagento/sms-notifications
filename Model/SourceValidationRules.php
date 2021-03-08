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
use Magento\Framework\Validator\AlnumFactory as AlphanumericValidatorFactory;
use Magento\Framework\Validator\DataObject as ValidatorObject;
use Magento\Framework\Validator\IntUtilsFactory as IntegerValidatorFactory;
use Magento\Framework\Validator\NotEmpty as NotEmptyValidator;
use Magento\Framework\Validator\NotEmptyFactory as NotEmptyValidatorFactory;
use Magento\Framework\Validator\Regex as RegexValidator;
use Magento\Framework\Validator\RegexFactory as RegexValidatorFactory;
use Magento\Framework\Validator\StringLength as LengthValidator;
use Magento\Framework\Validator\StringLengthFactory as LengthValidatorFactory;

/**
 * Source Configuration Field Validation Rules
 *
 * @package Wagento\SMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class SourceValidationRules implements ValidationRulesInterface
{
    private const FIELD = 'source';

    /**
     * @var \Magento\Framework\Validator\IntUtilsFactory
     */
    private $integerValidatorFactory;
    /**
     * @var \Magento\Framework\Validator\StringLengthFactory
     */
    private $lengthValidatorFactory;
    /**
     * @var \Magento\Framework\Validator\AlnumFactory
     */
    private $alphanumericValidatorFactory;
    /**
     * @var \Magento\Framework\Validator\NotEmptyFactory
     */
    private $notEmptyValidatorFactory;
    /**
     * @var \Magento\Framework\Validator\RegexFactory
     */
    private $regexValidatorFactory;

    public function __construct(
        NotEmptyValidatorFactory $notEmptyValidatorFactory,
        IntegerValidatorFactory $integerValidatorFactory,
        LengthValidatorFactory $lengthValidatorFactory,
        AlphanumericValidatorFactory $alphanumericValidatorFactory,
        RegexValidatorFactory $regexValidatorFactory
    ) {
        $this->integerValidatorFactory = $integerValidatorFactory;
        $this->lengthValidatorFactory = $lengthValidatorFactory;
        $this->alphanumericValidatorFactory = $alphanumericValidatorFactory;
        $this->notEmptyValidatorFactory = $notEmptyValidatorFactory;
        $this->regexValidatorFactory = $regexValidatorFactory;
    }

    /**
     * @throws \Zend_Validate_Exception
     */
    public function addSourceIsValidRules(ValidatorObject $validator, string $sourceType): ValidatorObject
    {
        /** @var \Magento\Framework\Validator\NotEmpty $isRequiredRule */
        $isRequiredRule = $this->notEmptyValidatorFactory->create(['options' => NotEmptyValidator::STRING]);

        $isRequiredRule->setMessage(__('Source is required.'), NotEmptyValidator::IS_EMPTY);

        $validator->addRule($isRequiredRule, self::FIELD);

        if ($sourceType === 'SHORTNUMBER') {
            $this->addShortNumberRules($validator);
        }

        if ($sourceType === 'ALPHANUMERIC') {
            $this->addAlphanumericRules($validator);
        }

        if ($sourceType === 'MSISDN') {
            $this->addPhoneNumberRules($validator);
        }

        return $validator;
    }

    /**
     * @throws \Zend_Validate_Exception
     */
    private function addShortNumberRules(ValidatorObject $validator): void
    {
        /** @var \Magento\Framework\Validator\IntUtils $isIntegerRule */
        $isIntegerRule = $this->integerValidatorFactory->create();
        /** @var \Magento\Framework\Validator\StringLength $lengthRule */
        $lengthRule = $this->lengthValidatorFactory->create(['options' => ['min' => 1, 'max' => 14]]);

        $isIntegerRule->setMessage(__('Source must be a numeric short number.'));

        $lengthRule->setMessage(__('Source is too short.'), LengthValidator::TOO_SHORT);
        $lengthRule->setMessage(__('Source is too long.'), LengthValidator::TOO_LONG);

        $validator->addRule($isIntegerRule, self::FIELD);
        $validator->addRule($lengthRule, self::FIELD);
    }

    /**
     * @throws \Zend_Validate_Exception
     */
    private function addAlphanumericRules(ValidatorObject $validator): void
    {
        /** @var \Magento\Framework\Validator\Alnum $isAlphanumericRule */
        $isAlphanumericRule = $this->alphanumericValidatorFactory->create();
        $lengthRule = $this->lengthValidatorFactory->create(['options' => ['min' => 1, 'max' => 11]]);

        $isAlphanumericRule->setMessage(__('Source may only contain the characters A-Z or a-z, or digits 0-9.'));

        $lengthRule->setMessage(__('Source is too short.'), LengthValidator::TOO_SHORT);
        $lengthRule->setMessage(__('Source is too long.'), LengthValidator::TOO_LONG);

        $validator->addRule($isAlphanumericRule, self::FIELD);
        $validator->addRule($lengthRule, self::FIELD);
    }

    /**
     * @throws \Zend_Validate_Exception
     */
    private function addPhoneNumberRules(ValidatorObject $validator): void
    {
        /** @var \Magento\Framework\Validator\Regex $startsWithPlusRule */
        $startsWithPlusRule = $this->regexValidatorFactory->create(['pattern' => '/^\+/']);

        $startsWithPlusRule->setMessage(__('Source must start with a plus ("+").'), RegexValidator::NOT_MATCH);

        $validator->addRule($startsWithPlusRule, self::FIELD);
    }
}
