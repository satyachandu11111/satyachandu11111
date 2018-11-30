<?php

namespace Mirasvit\Feed\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime;
use Mirasvit\Feed\Api\Data\ValidationInterface;

class Validation extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ValidationInterface::TABLE_NAME, ValidationInterface::ID);
    }
}
