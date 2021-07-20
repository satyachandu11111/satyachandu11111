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
namespace Aheadworks\Raf\Plugin\Model\Quote\Total;

use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Aheadworks\Raf\Model\Quote\Total\SubsequentDiscount;

/**
 * Class AbstactResetTotal
 *
 * @package Aheadworks\Raf\Plugin\Model\Quote\Total
 */
class AbstractResetTotalPlugin implements ResetTotalInterface
{
    /**
     * @var bool
     */
    protected $discountUsed;

    /**
     * @var array
     */
    protected $items;

    /**
     * @var SubsequentDiscount
     */
    protected $subsequentDiscount;

    /**
     * @param SubsequentDiscount $subsequentDiscount
     */
    public function __construct(
        SubsequentDiscount $subsequentDiscount
    ) {
        $this->subsequentDiscount = $subsequentDiscount;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeCollect(
        AbstractTotal $subject,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        $this->resetItems($quote, $shippingAssignment, $total);
        return [$quote, $shippingAssignment, $total];
    }

    /**
     * {@inheritdoc}
     */
    public function afterCollect(
        AbstractTotal $subject,
        AbstractTotal $result,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        $this->restoreItems($quote, $shippingAssignment, $total);
        return $result;
    }

    /**
     * Reset shipping assignment items
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     */
    protected function resetItems(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        if (!$this->subsequentDiscount->canBeApplied($quote, $shippingAssignment)) {
            if ($shippingAssignment->getItems()) {
                $this->updateBeforeReset($quote, $shippingAssignment, $total);
                $this->items = $shippingAssignment->getItems();
                $shippingAssignment->setItems([]);
            }
        }
    }

    /**
     * Restore shipping assignment items
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     */
    protected function restoreItems(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        if (!$this->subsequentDiscount->canBeApplied($quote, $shippingAssignment) && $this->items) {
            $this->updateBeforeRestore($quote, $shippingAssignment, $total);
            $shippingAssignment->setItems($this->items);
        }
    }

    /**
     * Update before reset
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return bool
     */
    protected function updateBeforeReset(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        return true;
    }

    /**
     * Update before restore
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return bool
     */
    protected function updateBeforeRestore(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        if ($this->discountUsed) {
            $quote->setAwRafThrowException(true);
        }
        return true;
    }
}
