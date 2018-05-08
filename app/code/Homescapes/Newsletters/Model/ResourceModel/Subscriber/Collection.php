<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Homescapes\Newsletters\Model\ResourceModel\Subscriber;



/**
 * Newsletter subscribers collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @api
 * @since 100.0.2
 */
class Collection extends \Magento\Newsletter\Model\ResourceModel\Subscriber\Grid\Collection
{
	

	protected function _initSelect()
    {
    	
        parent::_initSelect();
        
        $this->getSelect()->columns(
         array(
             'c_first' => new \Zend_Db_Expr('IFNULL(`customer`.`firstname`, `main_table`.c_firstname)')
         ));
        $this->getSelect()->columns(
         array(
             'c_last' => new \Zend_Db_Expr('IFNULL(`customer`.`lastname`, `main_table`.c_lastname)')
         ));
        return $this;
    }


	public function addFieldToFilter($field, $condition = null)
    {
       	if ($field === 'c_first') {
       
            $keyFilter = key($condition);
            $searchTerm = $condition[$keyFilter]->__toString();
            $this->getSelect()->where("`c_firstname` LIKE $searchTerm or `firstname` LIKE $searchTerm");
             return;
         }
         if ($field === 'c_last') {
       
            $keyFilter = key($condition);
            $searchTerm = $condition[$keyFilter]->__toString();
            $this->getSelect()->where("`c_lastname` LIKE $searchTerm or `lastname` LIKE $searchTerm");
             return;
         }

          return parent::addFieldToFilter($field, $condition);
    }
}
