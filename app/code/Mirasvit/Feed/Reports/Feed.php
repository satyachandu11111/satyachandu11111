<?php
namespace Mirasvit\Feed\Reports;

use Mirasvit\Report\Model\AbstractReport;
use Mirasvit\Report\Model\Select\Column;

class Feed extends AbstractReport
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Feed');
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'feed_overview';
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setTable('mst_feed_report');

        $this->addFastFilters([
            'mst_feed_report|created_at',
            'mst_feed_report|feed_id',
        ]);

        $this->setDefaultColumns([
            'mst_feed_report|is_click__sum',
            'mst_feed_report|unique_orders',
            'mst_feed_report|subtotal__sum',
            'mst_feed_report|subtotal_per_click',
        ]);

        $this->setDefaultDimension('mst_feed_report|created_at__day');

        $this->addDimensions([
            'mst_feed_report|created_at__day',
            'mst_feed_report|created_at__week',
            'mst_feed_report|created_at__month',
            'mst_feed_report|created_at__year',
            'mst_feed_feed|name',
            'catalog_product_entity|sku',
        ]);

        $this->getChartConfig()
            ->setType('column')
            ->setDefaultColumns([
                'mst_feed_report|is_click__sum'
            ]);
    }
}
