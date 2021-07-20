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
namespace Aheadworks\Raf\Model\Source\Order;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory as OrderStatusCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Status\Collection as OrderStatusCollection;

/**
 * Class Status
 * @package Aheadworks\Raf\Model\Source\Order
 */
class Status implements OptionSourceInterface
{
    /**
     * @var OrderStatusCollection
     */
    private $orderStatusCollection;

    /**
     * @var array
     */
    private $options;

    /**
     * @param OrderStatusCollectionFactory $orderStatusCollectionFactory
     */
    public function __construct(OrderStatusCollectionFactory $orderStatusCollectionFactory)
    {
        $this->orderStatusCollection = $orderStatusCollectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (null === $this->options) {
            $this->options = $this->orderStatusCollection->toOptionArray();
        }

        return $this->options;
    }
}
