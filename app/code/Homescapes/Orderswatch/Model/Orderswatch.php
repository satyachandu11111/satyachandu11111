<?php
namespace Homescapes\Orderswatch\Model;
class Orderswatch extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'homescapes_orderswatch';

	protected $_cacheTag = 'homescapes_orderswatch';

	protected $_eventPrefix = 'homescapes_orderswatch';

	protected function _construct()
	{
		$this->_init('Homescapes\Orderswatch\Model\ResourceModel\Orderswatch');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	
}

