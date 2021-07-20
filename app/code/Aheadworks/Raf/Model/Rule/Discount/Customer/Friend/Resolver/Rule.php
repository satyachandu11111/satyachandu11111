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
namespace Aheadworks\Raf\Model\Rule\Discount\Customer\Friend\Resolver;

use Aheadworks\Raf\Api\Data\RuleInterface;
use Aheadworks\Raf\Model\Metadata\Rule as RuleMetadata;
use Aheadworks\Raf\Model\Rule\Discount\Customer\Resolver\AbstractRule;
use Aheadworks\Raf\Model\Source\Rule\FriendOffType;

/**
 * Class Rule
 *
 * @package Aheadworks\Raf\Model\Rule\Discount\Customer\Friend\Resolver
 */
class Rule extends AbstractRule
{
    /**
     * {@inheritdoc}
     */
    protected function prepareData($quote, $address)
    {
        /** @var RuleInterface $rule */
        $rule = $quote->getAwRafRuleToApply();
        $isPercentDiscount = $rule->getFriendOffType() == FriendOffType::PERCENT;
        $shippingDiscountAmount = $isPercentDiscount ? $rule->getFriendOff() : 0;
        $ruleData = [
            RuleMetadata::ID => $rule->getId(),
            RuleMetadata::DISCOUNT_AMOUNT => $rule->getFriendOff(),
            RuleMetadata::DISCOUNT_TYPE => $rule->getFriendOffType(),
            RuleMetadata::IS_APPLY_TO_SHIPPING => $rule->isApplyToShipping(),
            RuleMetadata::SHIPPING_DISCOUNT_AMOUNT => $shippingDiscountAmount,
            RuleMetadata::CAN_FIX_SHIPPING_DISCOUNT_AMOUNT => !$isPercentDiscount
        ];

        return $ruleData;
    }
}
