<?php

namespace Mirasvit\Feed\Model\ResourceModel\Validation;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mirasvit\Feed\Api\Data\ValidationInterface;

class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = ValidationInterface::ID;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(
            \Mirasvit\Feed\Model\Validation::class,
            \Mirasvit\Feed\Model\ResourceModel\Validation::class
        );
    }
}
