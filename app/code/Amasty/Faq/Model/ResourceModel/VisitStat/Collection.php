<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\ResourceModel\VisitStat;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Faq\Model\VisitStat::class, \Amasty\Faq\Model\ResourceModel\VisitStat::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
