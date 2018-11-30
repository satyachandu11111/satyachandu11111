<?php
namespace Mirasvit\Feed\Model\ResourceModel\Dynamic\Category;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Feed\Model\Dynamic\Category', 'Mirasvit\Feed\Model\ResourceModel\Dynamic\Category');
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('mapping_id', 'name');
    } 
}
