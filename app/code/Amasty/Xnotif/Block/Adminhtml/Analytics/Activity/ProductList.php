<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


declare(strict_types=1);

namespace Amasty\Xnotif\Block\Adminhtml\Analytics\Activity;

use Magento\Backend\Block\Template;
use Amasty\Xnotif\Model\ResourceModel\Stock\Subscription\CollectionFactory as StockCollectionFactory;
use Amasty\Xnotif\Model\ResourceModel\Stock\Subscription\Collection as StockCollection;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class ProductList extends Template
{
    protected $_template = 'Amasty_Xnotif::analytics/activity.phtml';

    /**
     * @var StockCollectionFactory
     */
    private $stockCollectionFactory;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        StockCollectionFactory $stockCollectionFactory,
        Context $context,
        TimezoneInterface $timezone,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->stockCollectionFactory = $stockCollectionFactory;
        $this->timezone = $timezone;
    }

    /**
     * @param int $limit
     *
     * @return StockCollection
     */
    public function getLastSubscribers($limit = 10)
    {
        $stockCollection = $this->stockCollectionFactory->create();

        $stockCollection->getSelect()
            ->order('add_date DESC')
            ->limit($limit);

        return $stockCollection;
    }

    public function getAddDate($product): string
    {
        $date = $this->timezone->date(new \DateTime($product->getAddDate()));

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public function getMoreUrl()
    {
        return $this->getUrl('xnotif/subscription/index');
    }
}
