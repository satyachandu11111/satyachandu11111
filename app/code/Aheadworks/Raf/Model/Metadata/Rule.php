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
namespace Aheadworks\Raf\Model\Metadata;

use Magento\Framework\DataObject;

/**
 * Class Rule
 *
 * @package Aheadworks\Raf\Model\Metadata
 */
class Rule extends DataObject
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const DISCOUNT_TYPE = 'discount_type';
    const IS_APPLY_TO_SHIPPING = 'is_apply_to_shipping';
    const SHIPPING_DISCOUNT_AMOUNT = 'shipping_discount_amount';
    const CAN_FIX_SHIPPING_DISCOUNT_AMOUNT = 'can_fix_shipping_discount_amount';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountAmount()
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountType()
    {
        return $this->getData(self::DISCOUNT_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function isApplyToShipping()
    {
        return $this->getData(self::IS_APPLY_TO_SHIPPING);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingDiscountAmount()
    {
        return $this->getData(self::SHIPPING_DISCOUNT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingDiscountAmount($shippingDiscountAmount)
    {
        return $this->setData(self::SHIPPING_DISCOUNT_AMOUNT, $shippingDiscountAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function canFixShippingDiscountAmount()
    {
        return $this->getData(self::CAN_FIX_SHIPPING_DISCOUNT_AMOUNT);
    }
}
