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
namespace Aheadworks\Raf\Model\Rule\Discount\Calculator\Shipping;

use Aheadworks\Raf\Model\Metadata\Rule as MetadataRule;
use Magento\Quote\Api\Data\AddressInterface;

/**
 * Class Validator
 *
 * @package Aheadworks\Raf\Model\Rule\Discount\Calculator\Shipping
 */
class Validator
{
    /**
     * Can apply discount on shipping
     *
     * @param AddressInterface $address
     * @param MetadataRule $metadataRule
     * @return bool
     */
    public function canApplyDiscount($address, $metadataRule)
    {
        return $metadataRule->isApplyToShipping() && $metadataRule->getShippingDiscountAmount() > 0;
    }
}
