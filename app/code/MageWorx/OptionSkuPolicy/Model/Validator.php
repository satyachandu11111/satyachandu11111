<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionSkuPolicy\Model;

use Magento\Catalog\Api\Data\CustomOptionInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Option\Type\DefaultType;
use MageWorx\OptionSkuPolicy\Helper\Data as Helper;
use MageWorx\OptionBase\Model\ValidatorInterface;

class Validator implements ValidatorInterface
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Run validation process for add to cart action
     *
     * @param DefaultType $subject
     * @param array $values
     * @return bool
     */
    public function canValidateAddToCart($subject, $values)
    {
        return $this->process($subject->getProduct(), $subject->getOption());
    }

    /**
     * Run validation process for cart and checkout
     *
     * @param ProductInterface $product
     * @param CustomOptionInterface $option
     * @return bool
     */
    public function canValidateCartCheckout($product, $option)
    {
        return $this->process($product, $option);
    }

    /**
     * Check option sku policy, if independent or grouped - skip validation
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    protected function process($product, $option)
    {
        if (!$this->helper->isEnabledSkuPolicy()) {
            return true;
        }

        $skuPolicy = $option->getSkuPolicy();
        if ($option->getSkuPolicy() == Helper::SKU_POLICY_USE_CONFIG) {
            if ($product->getSkuPolicy() == Helper::SKU_POLICY_USE_CONFIG) {
                $skuPolicy = $this->helper->getDefaultSkuPolicy();
            } else {
                $skuPolicy = $product->getSkuPolicy();
            }
        }

        if ($skuPolicy == Helper::SKU_POLICY_INDEPENDENT || $skuPolicy == Helper::SKU_POLICY_GROUPED) {
            return false;
        }

        return true;
    }
}
