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
namespace Aheadworks\Raf\Model\Source\Transaction;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Action
 *
 * @package Aheadworks\Raf\Model\Source\Transaction
 */
class Action implements OptionSourceInterface
{
    /**#@+
     * Transaction action values
     */
    const EXPIRED = 'expired';
    const ADJUSTED_BY_ADMIN = 'adjusted_by_admin';
    const ADVOCATE_EARNED_FOR_FRIEND_ORDER = 'advocate_earned_for_friend_order';
    const ADVOCATE_SPENT_DISCOUNT_ON_ORDER = 'advocate_spent_discount_on_order';
    const ADVOCATE_REFUND_DISCOUNT_FOR_CANCELED_ORDER = 'advocate_refund_discount_for_canceled_order';
    const ADVOCATE_REFUND_DISCOUNT_FOR_CREDITMEMO = 'advocate_refund_discount_for_creditmemo';
    /**#@-*/

    /**
     *  {@inheritDoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::EXPIRED,
                'label' => __('Expired')
            ],
            [
                'value' => self::ADJUSTED_BY_ADMIN,
                'label' => __('Adjusted by Admin')
            ],
            [
                'value' => self::ADVOCATE_EARNED_FOR_FRIEND_ORDER,
                'label' => __('Advocate Earned for Friend Order')
            ],
            [
                'value' => self::ADVOCATE_SPENT_DISCOUNT_ON_ORDER,
                'label' => __('Advocate Spent Discount On Order')
            ],
            [
                'value' => self::ADVOCATE_REFUND_DISCOUNT_FOR_CANCELED_ORDER,
                'label' => __('Refund Discount to Advocate for Canceled Order')
            ],
            [
                'value' => self::ADVOCATE_REFUND_DISCOUNT_FOR_CREDITMEMO,
                'label' => __('Refund Discount to Advocate for Credit Memo')
            ]
        ];
    }

    /**
     * Prepare list with actions which contains placeholders for rendering
     *
     * @return array
     */
    public function getActionListWithCommentPlaceholders()
    {
        return [
            self::ADVOCATE_SPENT_DISCOUNT_ON_ORDER,
            self::ADVOCATE_EARNED_FOR_FRIEND_ORDER,
            self::ADVOCATE_REFUND_DISCOUNT_FOR_CANCELED_ORDER,
            self::ADVOCATE_REFUND_DISCOUNT_FOR_CREDITMEMO,
            self::EXPIRED
        ];
    }
}
