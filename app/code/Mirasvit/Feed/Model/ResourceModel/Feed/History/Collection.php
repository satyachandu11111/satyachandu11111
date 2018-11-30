<?php

namespace Mirasvit\Feed\Model\ResourceModel\Feed\History;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Feed\Model\Feed\History', 'Mirasvit\Feed\Model\ResourceModel\Feed\History');
    }
}
