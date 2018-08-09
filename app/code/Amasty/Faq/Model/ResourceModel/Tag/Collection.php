<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\ResourceModel\Tag;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @method \Amasty\Faq\Model\Tag[] getItems()
 */
class Collection extends AbstractCollection
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Faq\Model\Tag::class, \Amasty\Faq\Model\ResourceModel\Tag::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
