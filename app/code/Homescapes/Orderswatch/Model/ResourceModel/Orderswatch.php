<?php
namespace Homescapes\Orderswatch\Model\ResourceModel;


class Orderswatch extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('krish_orderswatch_sample', 'sample_id');
	}
	
}
