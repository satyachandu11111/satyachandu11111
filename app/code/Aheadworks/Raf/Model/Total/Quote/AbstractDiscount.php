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
namespace Aheadworks\Raf\Model\Total\Quote;

use Aheadworks\Raf\Api\RuleManagementInterface;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Aheadworks\Raf\Model\Rule\Discount\Customer\AbstractCalculator as RuleCustomerCalculator;
use Aheadworks\Raf\Model\Rule\Discount\ItemsApplier as RuleItemsApplier;
use Aheadworks\Raf\Model\Metadata\Rule\Discount as MetadataRuleDiscount;

/**
 * Class AbstractDiscount
 *
 * @package Aheadworks\Raf\Model\Total\Quote
 */
abstract class AbstractDiscount extends AbstractTotal
{
    /**
     * @var string
     */
    const IS_FRIEND_DISCOUNT = 'is_friend_discount';

    /**
     * @var RuleManagementInterface
     */
    protected $ruleManagement;

    /**
     * @var RuleCustomerCalculator
     */
    protected $ruleCustomerCalculator;

    /**
     * @var RuleItemsApplier
     */
    protected $ruleItemsApplier;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var bool
     */
    protected $isFirstTimeResetRun = true;

    /**
     * @param RuleManagementInterface $ruleManagement
     * @param RuleCustomerCalculator $ruleCustomerCalculator
     * @param RuleItemsApplier $ruleItemsApplier
     * @param Registry $registry
     */
    public function __construct(
        RuleManagementInterface $ruleManagement,
        RuleCustomerCalculator $ruleCustomerCalculator,
        RuleItemsApplier $ruleItemsApplier,
        Registry $registry
    ) {
        $this->setCode('aw_raf');
        $this->ruleManagement = $ruleManagement;
        $this->ruleCustomerCalculator = $ruleCustomerCalculator;
        $this->ruleItemsApplier = $ruleItemsApplier;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        $this->setCode('aw_raf');
        if (!$this->canProcess()) {
            return $this;
        }
        parent::collect($quote, $shippingAssignment, $total);

        $address = $shippingAssignment->getShipping()->getAddress();
        $items = $shippingAssignment->getItems();
        $this->reset($quote, $address, $items);

        if (!count($items)) {
            return $this;
        }

        $rule = $this->ruleManagement->getActiveRule($quote->getStore()->getWebsiteId());
        $metadataRuleDiscount = $this->ruleCustomerCalculator->calculateDiscount($items, $address, $quote, $rule);
        if (!$metadataRuleDiscount->isDiscountAvailable()) {
            $this->reset($quote, $address, $items, true);
            return $this;
        }

        $this->ruleItemsApplier->apply($items, $metadataRuleDiscount);

        $address
            ->setAwRafShippingPercent($metadataRuleDiscount->getShippingPercent())
            ->setAwRafShippingAmount($metadataRuleDiscount->getShippingAmount())
            ->setBaseAwRafShippingAmount($metadataRuleDiscount->getBaseShippingAmount())
            ->setAwRafAmount($total->getAwRafAmount())
            ->setBaseAwRafAmount($total->getBaseAwRafAmount());

        $this
            ->_addAmount(-$metadataRuleDiscount->getAmount())
            ->_addBaseAmount(-$metadataRuleDiscount->getBaseAmount());

        $total
            ->setSubtotalWithDiscount($total->getSubtotalWithDiscount() + $total->getAwRafAmount())
            ->setBaseSubtotalWithDiscount($total->getBaseSubtotalWithDiscount() + $total->getBaseAwRafAmount());

        $quote
            ->setAwRafAmount($total->getAwRafAmount())
            ->setBaseAwRafAmount($total->getBaseAwRafAmount())
            ->setAwRafIsFriendDiscount($metadataRuleDiscount->isFriendDiscount())
            ->setAwRafAmountType($metadataRuleDiscount->getAmountType())
            ->setAwRafPercentAmount(-$metadataRuleDiscount->getPercentAmount());

        $this->registry->register(self::IS_FRIEND_DISCOUNT, $metadataRuleDiscount->isFriendDiscount(), true);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(Quote $quote, Total $total)
    {
        $this->setCode('aw_raf');
        $amount = $total->getAwRafAmount();
        if ($amount != 0) {
            return [
                'code' => $this->getCode(),
                'title' => __('Referral Discount'),
                'value' => $amount
            ];
        }

        return null;
    }

    /**
     * Check if process
     *
     * @return bool
     */
    protected function canProcess()
    {
        return true;
    }

    /**
     * Reset RAF totals
     *
     * @param Quote $quote
     * @param AddressInterface $address
     * @param \Magento\Quote\Api\Data\CartItemInterface[] $items
     * @param bool $reset
     * @return $this
     */
    protected function reset(Quote $quote, AddressInterface $address, $items, $reset = false)
    {
        if ($this->isFirstTimeResetRun || $reset) {
            $this->_addAmount(0);
            $this->_addBaseAmount(0);

            $quote->setAwRafAmount(0);
            $quote->setBaseAwRafAmount(0);
            $quote->setAwRafIsFriendDiscount(null);

            $address->setAwRafAmount(0);
            $address->setBaseAwRafAmount(0);
            $address->setAwRafShippingAmount(0);
            $address->setBaseAwRafShippingAmount(0);

            /** @var \Magento\Quote\Model\Quote\Item $item */
            foreach ($items as $item) {
                $item->setAwRafAmount(0);
                $item->setBaseAwRafAmount(0);
                $item->setAwRafPercent(0);
                $item->setAwRafRuleIds(null);
                $address = $item->getAddress();
                $address->setAwRafRuleIds(null);

                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $child->setAwRafAmount(0);
                        $child->setBaseAwRafAmount(0);
                    }
                }
            }
            $this->isFirstTimeResetRun = false;
        }
        return $this;
    }
}
