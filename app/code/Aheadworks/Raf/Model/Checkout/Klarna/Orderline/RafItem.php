<?php
namespace Aheadworks\Raf\Model\Checkout\Klarna\Orderline;

use Klarna\Core\Api\BuilderInterface;
use Magento\Quote\Model\Quote;
use Klarna\Core\Model\Checkout\Orderline\AbstractLine;

/**
 * Class RafItem
 *
 * @package Aheadworks\Raf\Model\Checkout\Klarna\Orderline
 */
class RafItem extends AbstractLine
{
    /**
     * Checkout item type
     */
    const ITEM_TYPE_AHEADWORKS = 'discount';

    /**
     * Discount is a line item collector
     *
     * @var bool
     */
    protected $isTotalCollector = false;

    /**
     * Collect totals process
     *
     * @param BuilderInterface $checkout
     * @return $this
     */
    public function collect(BuilderInterface $checkout)
    {
        /** @var Quote $quote */
        $quote = $checkout->getObject();
        $totals = $quote->getTotals();
        if (is_array($totals)){
            if(isset($totals['aw_raf'])) {
                $total = $totals['aw_raf'];
                $value = $this->helper->toApiFloat($total->getValue());
                $checkout->addData([
                    'aw_raf_unit_price'   => $value,
                    'aw_raf_tax_rate'     => 0,
                    'aw_raf_total_amount' => $value,
                    'aw_raf_tax_amount'   => 0,
                    'aw_raf_title'        => $total->getTitle(),
                    'aw_raf_reference'    => $total->getCode()
                ]);
            }
        }

        return $this;
    }

    /**
     * Add order details to checkout request
     *
     * @param BuilderInterface $checkout
     * @return $this
     */
    public function fetch(BuilderInterface $checkout)
    {
        if ($checkout->getData('aw_raf_reference') && $checkout->getData('aw_raf_total_amount') !== 0) {
            $checkout->addOrderLine(
                [
                    'type'             => self::ITEM_TYPE_AHEADWORKS,
                    'reference'        => $checkout->getData('aw_raf_reference'),
                    'name'             => $checkout->getData('aw_raf_title'),
                    'quantity'         => 1,
                    'unit_price'       => $checkout->getData('aw_raf_unit_price'),
                    'tax_rate'         => $checkout->getData('aw_raf_tax_rate'),
                    'total_amount'     => $checkout->getData('aw_raf_total_amount'),
                    'total_tax_amount' => $checkout->getData('aw_raf_tax_amount'),
                ]
            );
        }

        return $this;
    }
}
