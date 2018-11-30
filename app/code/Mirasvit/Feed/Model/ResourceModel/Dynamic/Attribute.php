<?php

namespace Mirasvit\Feed\Model\ResourceModel\Dynamic;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime;

class Attribute extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('mst_feed_dynamic_attribute', 'attribute_id');
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->getData('conditions') && is_array($object->getData('conditions'))) {
            $object->setData('conditions_serialized', serialize($object->getData('conditions')));
        }

        return parent::_beforeSave($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad(AbstractModel $object)
    {
        if ($object->getData('conditions_serialized')) {
            $object->setData('conditions', unserialize($object->getData('conditions_serialized')));
        }

        return parent::_afterLoad($object);
    }
}
