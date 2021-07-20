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
namespace Aheadworks\Raf\Model\ResourceModel\Transaction;

use Aheadworks\Raf\Model\ResourceModel\AbstractCollection;
use Aheadworks\Raf\Model\ResourceModel\Transaction as ResourceTransaction;
use Aheadworks\Raf\Model\Transaction;

/**
 * Class Collection
 * @package Aheadworks\Raf\Model\ResourceModel\Transaction
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Transaction::class, ResourceTransaction::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['aw_raf_summary_table' => $this->getTable('aw_raf_summary')],
            'main_table.summary_id = aw_raf_summary_table.id',
            ['website_id']
        );
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            'aw_raf_transaction_entity',
            'id',
            'transaction_id',
            ['entity_type', 'entity_id', 'entity_label'],
            'entities'
        );

        return parent::_afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'id') {
            $field = 'main_table.' . $field;
        }
        return parent::addFieldToFilter($field, $condition);
    }
}
