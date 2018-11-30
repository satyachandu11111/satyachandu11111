<?php
namespace Mirasvit\Feed\Model\ResourceModel\Dynamic\Variable;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Feed\Model\Dynamic\Variable', 'Mirasvit\Feed\Model\ResourceModel\Dynamic\Variable');
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('variable_id', 'name');
    }     
}
