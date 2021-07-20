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
namespace Aheadworks\Raf\Model\Rule\Discount\Calculator\Item;

use Magento\Quote\Model\Quote\Item\AbstractItem;
use Aheadworks\Raf\Model\Metadata\Rule as MetadataRule;

/**
 * Class Validator
 *
 * @package Aheadworks\Raf\Model\Rule\Discount\Calculator\Item
 */
class Validator
{
    /**
     * Can apply discount on item
     *
     * @param AbstractItem $item
     * @param MetadataRule $metadataRule
     * @return bool
     */
    public function canApplyDiscount($item, $metadataRule)
    {
        return true;
    }
}
