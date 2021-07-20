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
namespace Aheadworks\Raf\Plugin\Block\Tax\Item;

use Magento\Sales\Model\Order\CreditMemo\Item as CreditMemoItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Tax\Block\Item\Price\Renderer;

/**
 * Class PriceRendererPlugin
 *
 * @package Aheadworks\Raf\Plugin\Block\Tax\Item
 */
class PriceRendererPlugin
{
    /**
     * Subtract RAF data
     *
     * @param Renderer $subject
     * @param \Closure $proceed
     * @param OrderItem|InvoiceItem|CreditMemoItem $item
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetTotalAmount(
        Renderer $subject,
        \Closure $proceed,
        $item
    ) {
        $totalAmount = $proceed($item);
        // Convert to the same type
        return (string)(float)$totalAmount - (string)(float)$item->getAwRafAmount();
    }

    /**
     * Subtract RAF data
     *
     * @param Renderer $subject
     * @param \Closure $proceed
     * @param OrderItem|InvoiceItem|CreditMemoItem $item
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetBaseTotalAmount(
        Renderer $subject,
        \Closure $proceed,
        $item
    ) {
        $totalAmount = $proceed($item);
        // Convert to the same type
        return (string)(float)$totalAmount - (string)(float)$item->getAwRafAmount();
    }
}
