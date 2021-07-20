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
namespace Aheadworks\Raf\Model\ResourceModel\AdvocateSummary;

use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aheadworks\Raf\Model\ResourceModel\AdvocateSummary as ResourceAdvocateSummary;
use Aheadworks\Raf\Model\AdvocateSummary;

/**
 * Class Collection
 *
 * @package Aheadworks\Raf\Model\ResourceModel\AdvocateSummary
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(AdvocateSummary::class, ResourceAdvocateSummary::class);
        $this->addFilterToMap('customer_email', 'email');
        $this->addFilterToMap('customer_name', 'name');
        $this->addFilterToMap('website_id', 'main_table.website_id');
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['customer_grid_table' => $this->getTable('customer_grid_flat')],
            'main_table.customer_id = customer_grid_table.entity_id',
            ['customer_email' => 'email', 'customer_name' => 'name']
        );
        return $this;
    }

    /**
     * Add expire filter to collection
     *
     * @param string $expiredDate
     * @return $this
     */
    public function addExpireFilter($expiredDate)
    {
        $this->addFieldToFilter(AdvocateSummaryInterface::EXPIRATION_DATE, ['notnull' => true]);
        $this->getSelect()->where('DATE(' . AdvocateSummaryInterface::EXPIRATION_DATE . ') < ?', $expiredDate);

        return $this;
    }
}
