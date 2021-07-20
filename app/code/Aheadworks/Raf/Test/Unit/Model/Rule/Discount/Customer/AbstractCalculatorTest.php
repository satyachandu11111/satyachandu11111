<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Raf
 * @version    1.1.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Raf\Test\Unit\Model\Rule\Discount\Customer;

use Aheadworks\Raf\Model\Rule\Discount\Customer\AbstractCalculator;
use Aheadworks\Raf\Api\Data\RuleInterface;
use Aheadworks\Raf\Model\Metadata\Rule\Discount as MetadataRuleDiscount;
use Aheadworks\Raf\Model\Rule\Discount\Calculator\Pool as CalculatorPool;
use Aheadworks\Raf\Model\Rule\Discount\Customer\Resolver\AbstractRule as AbstractRuleResolver;
use Magento\Quote\Model\Quote;
use Aheadworks\Raf\Model\Metadata\Rule\DiscountFactory as MetadataRuleDiscountFactory;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Framework\Validator\AbstractValidator;
use PHPUnit\Framework\TestCase;
use Aheadworks\Raf\Model\Metadata\Rule as RuleMetadata;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Raf\Model\Rule\Discount\Calculator\DiscountCalculatorInterface;
use Zend_Validate_Exception as ValidateException;

/**
 * Class AbstractCalculatorTest
 *
 * @package Aheadworks\Raf\Model\Rule\Discount\Customer
 */
class AbstractCalculatorTest extends TestCase
{
    /**
     * @var AbstractCalculator
     */
    private $object;

    /**
     * @var MetadataRuleDiscountFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataRuleDiscountFactoryMock;

    /**
     * @var CalculatorPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $calculatorPoolMock;

    /**
     * @var AbstractValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $validatorMock;

    /**
     * @var AbstractRuleResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->metadataRuleDiscountFactoryMock = $this->createPartialMock(
            MetadataRuleDiscountFactory::class,
            ['create']
        );
        $this->calculatorPoolMock = $this->createPartialMock(
            CalculatorPool::class,
            ['getCalculatorByType']
        );
        $this->validatorMock = $this->createPartialMock(
            AbstractValidator::class,
            ['isValid']
        );
        $this->ruleResolverMock = $this->createPartialMock(
            AbstractRuleResolver::class,
            ['resolve', 'prepareData']
        );

        $this->object = $objectManager->getObject(
            AbstractCalculator::class,
            [
                'metadataRuleDiscountFactory' => $this->metadataRuleDiscountFactoryMock,
                'calculatorPool' => $this->calculatorPoolMock,
                'validator' => $this->validatorMock,
                'ruleResolver' => $this->ruleResolverMock,
            ]
        );
    }

    /**
     * Test for testCalculate method
     */
    public function testCalculateDiscount()
    {
        $items = [
            $this->getMockForAbstractClass(CartItemInterface::class),
            $this->getMockForAbstractClass(CartItemInterface::class)
        ];
        $address = $this->getMockForAbstractClass(AddressInterface::class);
        $calculator = $this->getMockForAbstractClass(DiscountCalculatorInterface::class);
        $rule = $this->getMockForAbstractClass(RuleInterface::class);
        $metaDataRuleDiscountMock = $this->getMockForAbstractClass(MetadataRuleDiscount::class);
        $ruleMetaData = $this->createPartialMock(
            RuleMetadata::class,
            ['getDiscountType']
        );
        $quote = $this->createPartialMock(
            Quote::class,
            ['setAwRafRuleToApply']
        );

        $quote->expects($this->once())
            ->method('setAwRafRuleToApply')
            ->with($rule)
            ->willReturnSelf();
        $this->validatorMock->expects($this->once())
            ->method('isValid')
            ->with($quote)
            ->willReturn(true);
        $this->ruleResolverMock->expects($this->once())
            ->method('resolve')
            ->with($quote, $address)
            ->willReturn($ruleMetaData);
        $this->calculatorPoolMock->expects($this->once())
            ->method('getCalculatorByType')
            ->willReturn($calculator);
        $calculator->expects($this->once())
            ->method('calculate')
            ->with($items, $address, $ruleMetaData)
            ->willReturn($metaDataRuleDiscountMock);

        $this->assertSame(
            $metaDataRuleDiscountMock,
            $this->object->calculateDiscount($items, $address, $quote, $rule)
        );
    }

    /**
     * Test for testCalculate method in case quote validation is not passed
     */
    public function testCalculateDiscountOnFailedValidation()
    {
        $items = [
            $this->getMockForAbstractClass(CartItemInterface::class),
            $this->getMockForAbstractClass(CartItemInterface::class)
        ];
        $address = $this->getMockForAbstractClass(AddressInterface::class);
        $rule = $this->getMockForAbstractClass(RuleInterface::class);
        $metaDataRuleDiscountMock = $this->getMockForAbstractClass(MetadataRuleDiscount::class);
        $quote = $this->createPartialMock(
            Quote::class,
            ['setAwRafRuleToApply']
        );

        $quote->expects($this->once())
            ->method('setAwRafRuleToApply')
            ->with($rule)
            ->willReturnSelf();
        $this->validatorMock->expects($this->once())
            ->method('isValid')
            ->with($quote)
            ->willReturn(false);
        $this->metadataRuleDiscountFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($metaDataRuleDiscountMock);

        $this->assertSame(
            $metaDataRuleDiscountMock,
            $this->object->calculateDiscount($items, $address, $quote, $rule)
        );
    }

    /**
     * Test for testCalculate method in case exception is thrown
     */
    public function testCalculateDiscountOnException()
    {
        $exception = new ValidateException('some exception');
        $items = [
            $this->getMockForAbstractClass(CartItemInterface::class),
            $this->getMockForAbstractClass(CartItemInterface::class)
        ];
        $address = $this->getMockForAbstractClass(AddressInterface::class);
        $rule = $this->getMockForAbstractClass(RuleInterface::class);
        $quote = $this->createPartialMock(
            Quote::class,
            ['setAwRafRuleToApply']
        );

        $quote->expects($this->once())
            ->method('setAwRafRuleToApply')
            ->with($rule)
            ->willReturnSelf();
        $this->validatorMock->expects($this->once())
            ->method('isValid')
            ->with($quote)
            ->willThrowException($exception);

        $this->expectException(ValidateException::class);
        $this->object->calculateDiscount($items, $address, $quote, $rule);
    }
}
