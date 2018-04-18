<?php
namespace Homescapes\Completelook\Model\ResourceModel;


class Completelook extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('completelook_product', 'completelook_id');
	}
	
}
