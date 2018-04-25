<?php
namespace Homescapes\Completelook\Model;
class Completelook extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'homescapes_completelook';

	protected $_cacheTag = 'homescapes_completelook';

	protected $_eventPrefix = 'homescapes_completelook';
        
        const COMPLETE_LOOK_PRODUCT = 'completelook_product';

	protected function _construct()
	{
		$this->_init('Homescapes\Completelook\Model\ResourceModel\Completelook');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
        
//        public function getProducts(\Homescapes\Completelook\Model\Completelook $object)
//        {
//            print_r($object->getData()); die('qqqqq');
//            $tbl = $this->getResource()->getTable(self::COMPLETE_LOOK_PRODUCT);
//            $select = $this->getResource()->getConnection()->select()->from(
//                $tbl,
//                ['look_product_id']
//            )
//            ->where(
//                'product_id = ?',
//                (int)$object->getId()
//            );
//            return $this->getResource()->getConnection()->fetchCol($select);
//        }
}


