<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Test\Unit\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Test\Unit\Model;

use Wagento\SMSNotifications\Model\Config\Backend\Source as SourceModel;
use Wagento\SMSNotifications\Model\SourceValidationRules;
use Wagento\SMSNotifications\Model\SourceValidator;
use Magento\Framework\Validator\Alnum as AlphanumericValidator;
use Magento\Framework\Validator\AlnumFactory as AlphanumericValidatorFactory;
use Magento\Framework\Validator\DataObject as ValidatorObject;
use Magento\Framework\Validator\DataObjectFactory as ValidatorObjectFactory;
use Magento\Framework\Validator\IntUtils as IntegerValidator;
use Magento\Framework\Validator\IntUtilsFactory as IntegerValidatorFactory;
use Magento\Framework\Validator\NotEmpty as NotEmptyValidator;
use Magento\Framework\Validator\NotEmptyFactory as NotEmptyValidatorFactory;
use Magento\Framework\Validator\Regex as RegexValidator;
use Magento\Framework\Validator\RegexFactory as RegexValidatorFactory;
use Magento\Framework\Validator\StringLength as LengthValidator;
use Magento\Framework\Validator\StringLengthFactory as LengthValidatorFactory;
use PHPUnit\Framework\TestCase;

/**
 * Source Configuration Field Validator Test
 *
 * @package Wagento\SMSNotifications\Test\Unit\Model
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * phpcs:disable
 */
class SourceValidatorTest extends TestCase
{
    /** @var \Wagento\SMSNotifications\Model\SourceValidator */
    private $validator;

    /**
     * @dataProvider validSourceDataProvider
     * @throws \Zend_Validate_Exception
     */
    public function testValidateValidSource(string $sourceType, string $source): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|\Wagento\SMSNotifications\Model\Config\Backend\Source $sourceModel */
        $sourceModel = $this->getMockBuilder(SourceModel::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSource'])
            ->getMock();

        $sourceModel->method('getSource')->willReturn($source);

        $this->validator->setSourceType($sourceType)->validate($sourceModel);

        $this->assertTrue($this->validator->isValid());
        $this->assertEmpty($this->validator->getMessages());
    }

    /**
     * @dataProvider invalidSourceDataProvider
     * @throws \Zend_Validate_Exception
     */
    public function testValidateInvalidSource(string $sourceType, string $source, string $expectedError): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|\Wagento\SMSNotifications\Model\Config\Backend\Source $sourceModel */
        $sourceModel = $this->getMockBuilder(SourceModel::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSource'])
            ->getMock();

        $sourceModel->method('getSource')->willReturn($source);

        $this->validator->setSourceType($sourceType)->validate($sourceModel);

        $messages = $this->validator->getMessages();

        $this->assertFalse($this->validator->isValid());
        $this->assertNotEmpty($messages);
        $this->assertContains($expectedError, $messages);
    }

    public static function validSourceDataProvider(): array
    {
        return [
            'short number' => [
                'source_type' => 'SHORTNUMBER',
                'source' => '12345'
            ],
            'alphanumeric' => [
                'source_type' => 'ALPHANUMERIC',
                'source' => 'ABC123'
            ],
            'phone number' => [
                'source_type' => 'MSISDN',
                'source' => '+1555551234'
            ],
        ];
    }

    public static function invalidSourceDataProvider(): array
    {
        return [
            'empty' => [
                'source_type' => '',
                'source' => '',
                'expectedError' => __('Source is required.')
            ],
            'short number' => [
                'source_type' => 'SHORTNUMBER',
                'source' => 'ABCDEF',
                'expectedError' => __('Source must be a numeric short number.')
            ],
            'alphanumeric' => [
                'source_type' => 'ALPHANUMERIC',
                'source' => '+ABC123',
                'expectedError' => __('Source may only contain the characters A-Z or a-z, or digits 0-9.')
            ],
            'phone number' => [
                'source_type' => 'MSISDN',
                'source' => '1555551234',
                'expectedError' => __('Source must start with a plus ("+").')
            ],
        ];
    }

    protected function setUp()
    {
        parent::setUp();

        $validatorObjectFactory = $this->getMockBuilder(ValidatorObjectFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $notEmptyValidatorFactory = $this->getMockBuilder(NotEmptyValidatorFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $integerValidatorFactory = $this->getMockBuilder(IntegerValidatorFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $lengthValidatorFactory = $this->getMockBuilder(LengthValidatorFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $alphanumericValidatorFactory = $this->getMockBuilder(AlphanumericValidatorFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $regexValidatorFactory = $this->getMockBuilder(RegexValidatorFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $validatorObjectFactory->method('create')->willReturn(new ValidatorObject());
        $notEmptyValidatorFactory->method('create')->willReturnCallback(function ($arguments) {
            return new NotEmptyValidator($arguments['options']);
        });
        $integerValidatorFactory->method('create')->willReturn(new IntegerValidator());
        $lengthValidatorFactory->method('create')->willReturnCallback(function ($arguments) {
            return new LengthValidator($arguments['options']);
        });
        $alphanumericValidatorFactory->method('create')->willReturn(new AlphanumericValidator());
        $regexValidatorFactory->method('create')->willReturnCallback(function ($arguments) {
            return new RegexValidator($arguments['pattern']);
        });

        $validationRules = new SourceValidationRules(
            $notEmptyValidatorFactory,
            $integerValidatorFactory,
            $lengthValidatorFactory,
            $alphanumericValidatorFactory,
            $regexValidatorFactory
        );
        $this->validator = new SourceValidator($validatorObjectFactory, $validationRules);
    }
}
