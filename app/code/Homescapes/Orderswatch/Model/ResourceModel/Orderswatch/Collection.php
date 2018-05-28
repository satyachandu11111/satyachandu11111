<?php
namespace Homescapes\Orderswatch\Model\ResourceModel\Orderswatch;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'sample_id';
	protected $_eventPrefix = 'homescapes_orderswatch_collection';
	protected $_eventObject = 'krish_sample_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Homescapes\Orderswatch\Model\Orderswatch', 'Homescapes\Orderswatch\Model\ResourceModel\Orderswatch');
	}

}


