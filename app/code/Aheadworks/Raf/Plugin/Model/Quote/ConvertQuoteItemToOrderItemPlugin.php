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
namespace Aheadworks\Raf\Plugin\Model\Quote;

use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Sales\Model\Order\Item;

/**
 * Class ConvertQuoteItemToOrderItemPlugin
 *
 * @package Aheadworks\Raf\Plugin\Model\Quote
 */
class ConvertQuoteItemToOrderItemPlugin
{
    /**
     * @param ToOrderItem $subject
     * @param \Closure $proceed
     * @param AbstractItem $item
     * @param array $additional
     * @return Item
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundConvert(
        ToOrderItem $subject,
        \Closure $proceed,
        AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);

        $orderItem->setAwRafPercent($item->getAwRafPercent());
        $orderItem->setBaseAwRafAmount($item->getBaseAwRafAmount());
        $orderItem->setAwRafAmount($item->getAwRafAmount());
        $orderItem->setAwRafRuleIds($item->getAwRafRuleIds());

        return $orderItem;
    }
}
