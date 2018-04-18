<?php
namespace Homescapes\Completelook\Model\ResourceModel\Completelook;
          
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'completelook_id';
	protected $_eventPrefix = 'completelook_product_collection';
	protected $_eventObject = 'completelook_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Homescapes\Completelook\Model\Completelook', 'Homescapes\Completelook\Model\ResourceModel\Completelook');
	}

}



