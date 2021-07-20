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
namespace Aheadworks\Raf\Model\Rule\Discount\Calculator;

use Aheadworks\Raf\Model\Metadata\Rule as MetadataRule;
use Magento\Quote\Api\Data\AddressInterface;

/**
 * Interface ShippingCalculatorInterface
 *
 * @package Aheadworks\Raf\Model\Rule\Discount\Calculator
 */
interface ShippingCalculatorInterface
{
    /**
     * Calculate shipping discount
     *
     * @param AddressInterface $address
     * @param MetadataRule $metadataRule
     * @return array
     */
    public function calculate($address, $metadataRule);
}
