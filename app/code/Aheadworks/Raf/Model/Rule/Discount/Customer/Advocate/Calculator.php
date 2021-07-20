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
namespace Aheadworks\Raf\Model\Rule\Discount\Customer\Advocate;

use Aheadworks\Raf\Model\Rule\Discount\Customer\AbstractCalculator;
use Aheadworks\Raf\Model\Rule\Discount\Calculator\Pool as CalculatorPool;
use Aheadworks\Raf\Model\Metadata\Rule\DiscountFactory as MetadataRuleDiscountFactory;
use Aheadworks\Raf\Model\Rule\Discount\Customer\Advocate\Resolver\Rule as RuleResolver;

/**
 * Class Calculator
 *
 * @package Aheadworks\Raf\Model\Rule\Discount\Customer\Advocate
 */
class Calculator extends AbstractCalculator
{
    /**
     * @param MetadataRuleDiscountFactory $metadataRuleDiscountFactory
     * @param CalculatorPool $calculatorPool
     * @param Validator $validator
     * @param RuleResolver $ruleResolver
     */
    public function __construct(
        MetadataRuleDiscountFactory $metadataRuleDiscountFactory,
        CalculatorPool $calculatorPool,
        Validator $validator,
        RuleResolver $ruleResolver
    ) {
        parent::__construct($metadataRuleDiscountFactory, $calculatorPool, $validator, $ruleResolver);
    }
}
