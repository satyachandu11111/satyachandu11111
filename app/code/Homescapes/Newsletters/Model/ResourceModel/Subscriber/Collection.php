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
             'subscriber_firstname' => new \Zend_Db_Expr('IFNULL(`customer`.`firstname`, `main_table`.subscriber_firstname)')
         ));
        $this->getSelect()->columns(
         array(
             'subscriber_lastname' => new \Zend_Db_Expr('IFNULL(`customer`.`lastname`, `main_table`.subscriber_lastname)')
         ));
        return $this;
    }


	public function addFieldToFilter($field, $condition = null)
    {
       	if ($field === 'subscriber_firstname') {
       
            $keyFilter = key($condition);
            $searchTerm = $condition[$keyFilter]->__toString();
            $this->getSelect()->where("`subscriber_firstname` LIKE $searchTerm or `firstname` LIKE $searchTerm");
             return;
         }
         if ($field === 'subscriber_lastname') {
       
            $keyFilter = key($condition);
            $searchTerm = $condition[$keyFilter]->__toString();
            $this->getSelect()->where("`subscriber_lastname` LIKE $searchTerm or `lastname` LIKE $searchTerm");
             return;
         }

          return parent::addFieldToFilter($field, $condition);
    }
}
