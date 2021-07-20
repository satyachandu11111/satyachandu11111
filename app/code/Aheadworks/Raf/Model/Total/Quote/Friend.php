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
use Aheadworks\Raf\Model\Rule\Discount\Customer\Friend\Calculator as RuleFriendCalculator;
use Aheadworks\Raf\Model\Rule\Discount\ItemsApplier as RuleItemsApplier;
use Magento\Framework\Registry;

/**
 * Class Friend
 *
 * @package Aheadworks\Raf\Model\Total\Quote
 */
class Friend extends AbstractDiscount
{
    /**
     * @param RuleManagementInterface $ruleManagement
     * @param RuleFriendCalculator $ruleCustomerCalculator
     * @param RuleItemsApplier $ruleItemsApplier
     * @param Registry $registry
     */
    public function __construct(
        RuleManagementInterface $ruleManagement,
        RuleFriendCalculator $ruleCustomerCalculator,
        RuleItemsApplier $ruleItemsApplier,
        Registry $registry
    ) {
        parent::__construct($ruleManagement, $ruleCustomerCalculator, $ruleItemsApplier, $registry);
    }
}
