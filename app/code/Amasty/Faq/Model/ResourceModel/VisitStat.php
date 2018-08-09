<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Amasty\Faq\Setup\Operation;
use Amasty\Faq\Api\Data\VisitStatInterface;

class VisitStat extends AbstractDb
{
    public function _construct()
    {
        $this->_init(Operation\CreateViewStatTables::TABLE_NAME, VisitStatInterface::VISIT_ID);
    }
}
