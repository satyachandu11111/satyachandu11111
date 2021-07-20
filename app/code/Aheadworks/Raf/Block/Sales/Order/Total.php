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
namespace Aheadworks\Raf\Block\Sales\Order;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\DataObject\Factory;
use Magento\Sales\Model\Order;

/**
 * Class Total
 *
 * @package Aheadworks\Raf\Block\Sales\Order
 */
class Total extends Template
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @param Context $context
     * @param Factory $factory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $factory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->factory = $factory;
    }

    /**
     * Init totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $source = $this->getSource();
        if (!$source) {
            return $this;
        }

        if ($source->getBaseAwRafAmount()) {
            $this->getParentBlock()->addTotal(
                $this->factory->create(
                    [
                        'code'   => 'aw_raf_amount',
                        'strong' => false,
                        'label'  => __('Referral Discount'),
                        'value'  => $source->getAwRafAmount(),
                    ]
                )
            );
        }

        return $this;
    }

    /**
     * Retrieve totals source object
     *
     * @return Order|null
     */
    private function getSource()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock) {
            return $parentBlock->getSource();
        }
        return null;
    }
}
