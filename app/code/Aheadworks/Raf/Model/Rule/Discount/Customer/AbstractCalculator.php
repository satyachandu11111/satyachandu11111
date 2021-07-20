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
namespace Aheadworks\Raf\Model\Rule\Discount\Customer;

use Aheadworks\Raf\Api\Data\RuleInterface;
use Aheadworks\Raf\Model\Metadata\Rule\Discount as MetadataRuleDiscount;
use Aheadworks\Raf\Model\Rule\Discount\Calculator\Pool as CalculatorPool;
use Aheadworks\Raf\Model\Rule\Discount\Customer\Resolver\AbstractRule as AbstractRuleResolver;
use Magento\Quote\Model\Quote;
use Aheadworks\Raf\Model\Metadata\Rule\DiscountFactory as MetadataRuleDiscountFactory;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class AbstractCalculator
 *
 * @package Aheadworks\Raf\Model\Rule\Discount\Customer
 */
class AbstractCalculator
{
    /**
     * @var MetadataRuleDiscountFactory
     */
    private $metadataRuleDiscountFactory;

    /**
     * @var CalculatorPool
     */
    private $calculatorPool;

    /**
     * @var AbstractValidator
     */
    private $validator;

    /**
     * @var AbstractRuleResolver
     */
    private $ruleResolver;

    /**
     * @param MetadataRuleDiscountFactory $metadataRuleDiscountFactory
     * @param CalculatorPool $calculatorPool
     * @param AbstractValidator $validator
     * @param AbstractRuleResolver $ruleResolver
     */
    public function __construct(
        MetadataRuleDiscountFactory $metadataRuleDiscountFactory,
        CalculatorPool $calculatorPool,
        AbstractValidator $validator,
        AbstractRuleResolver $ruleResolver
    ) {
        $this->metadataRuleDiscountFactory = $metadataRuleDiscountFactory;
        $this->calculatorPool = $calculatorPool;
        $this->validator = $validator;
        $this->ruleResolver = $ruleResolver;
    }

    /**
     * Calculate discount
     *
     * @param CartItemInterface[]|AbstractItem[] $items
     * @param AddressInterface $address
     * @param Quote $quote
     * @param RuleInterface $rule
     * @return MetadataRuleDiscount
     * @throws \Zend_Validate_Exception
     */
    public function calculateDiscount($items, $address, $quote, $rule)
    {
        $quote->setAwRafRuleToApply($rule);
        if ($this->validator->isValid($quote) && is_array($items) && !empty($items)) {
            $metadataRule = $this->ruleResolver->resolve($quote, $address);
            $calculator = $this->calculatorPool->getCalculatorByType($metadataRule->getDiscountType());
            $metadataRuleDiscount = $calculator->calculate($items, $address, $metadataRule);
        } else {
            $metadataRuleDiscount = $this->metadataRuleDiscountFactory->create();
        }

        return $metadataRuleDiscount;
    }
}
