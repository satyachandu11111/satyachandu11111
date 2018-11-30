<?php
namespace Mirasvit\Feed\Model\ResourceModel\Dynamic\Attribute;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Feed\Model\Dynamic\Attribute', 'Mirasvit\Feed\Model\ResourceModel\Dynamic\Attribute');
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('attribute_id', 'name');
    }    
}
