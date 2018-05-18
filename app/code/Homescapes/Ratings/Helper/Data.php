<?php

namespace Homescapes\Ratings\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	protected $_resource;

	public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
    }

    public function getRecommend($reviewId)
    {
    	
        $connection = $this->_resource;

        $tableName = $connection->getTableName('review_detail');
        
        $select = $connection->getConnection()->select()->from($tableName)->where('review_id = '.$reviewId);
        
        $detail = $connection->getConnection()->fetchAll($select);
        
        $data =  reset($detail);
        
        return $data['recommend'];

    }
}

