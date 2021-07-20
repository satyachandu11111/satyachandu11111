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
namespace Aheadworks\Raf\Model\Advocate\Balance;

use Aheadworks\Raf\Api\RuleManagementInterface;
use Aheadworks\Raf\Api\Data\RuleInterface;
use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Aheadworks\Raf\Model\Source\Rule\AdvocateOffType;

/**
 * Class Resolve
 *
 * @package Aheadworks\Raf\Model\Advocate\Balance
 */
class Resolver
{
    /**
     * @var RuleManagementInterface
     */
    private $ruleManagement;

    /**
     * @param RuleManagementInterface $ruleManagement
     */
    public function __construct(
        RuleManagementInterface $ruleManagement
    ) {
        $this->ruleManagement = $ruleManagement;
    }

    /**
     * Resolve current discount type
     *
     * @param AdvocateSummaryInterface $advocate
     * @param int $websiteId
     * @return string
     */
    public function resolveCurrentDiscountType($advocate, $websiteId)
    {
        /** @var RuleInterface $activeRule */
        $activeRule = $this->ruleManagement->getActiveRule($websiteId);
        if ($activeRule) {
            $discountType = $this->resolveDiscountTypeByRule($advocate, $activeRule);
        } else {
            $discountType = $this->resolveDiscountTypeBySummary($advocate);
        }

        return $discountType;
    }

    /**
     * Resolve current discount type
     *
     * @param AdvocateSummaryInterface $advocate
     * @param int $websiteId
     * @return string
     */
    public function resolveCurrentBalance($advocate, $websiteId)
    {
        $discountType = $this->resolveCurrentDiscountType($advocate, $websiteId);
        $balance = 0;
        if ($discountType == AdvocateOffType::FIXED) {
            $balance = $advocate->getCumulativeAmount();
        }
        if ($discountType == AdvocateOffType::PERCENT) {
            $balance = $advocate->getCumulativePercentAmount();
        }

        return $balance;
    }

    /**
     * Resolve discount type by advocate summary balance
     *
     * @param AdvocateSummaryInterface $advocate
     * @return string
     */
    public function resolveDiscountTypeBySummary($advocate)
    {
        $discountType = AdvocateOffType::FIXED;
        if ($advocate->getCumulativePercentAmount()) {
            $discountType = AdvocateOffType::PERCENT;
        }

        return $discountType;
    }

    /**
     * Resolve discount type by advocate summary balance
     *
     * @param AdvocateSummaryInterface $advocate
     * @param RuleInterface $rule
     * @return string
     */
    public function resolveDiscountTypeByRule($advocate, $rule)
    {
        switch ($rule->getAdvocateOffType()) {
            case AdvocateOffType::FIXED:
                $discountType = $advocate->getCumulativePercentAmount()
                    ? AdvocateOffType::PERCENT
                    : AdvocateOffType::FIXED;
                break;
            case AdvocateOffType::PERCENT:
                $discountType = $advocate->getCumulativeAmount()
                    ? AdvocateOffType::FIXED
                    : AdvocateOffType::PERCENT;
                break;
            default:
                $discountType = AdvocateOffType::FIXED;
        }

        return $discountType;
    }
}
