<?php
namespace Homescapes\Newsletters\Block\Adminhtml\Template\Grid\Renderer;

use Magento\Framework\DataObject;

class Customerfirstname extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
    	
    	if($row->getData('type')==1){
            return ($row->getData('subscriber_firstname')!=''?$row->getData('subscriber_firstname'):'----');
        }else{
            return ($row->getData('firstname')!=''?$row->getData('firstname'):'----');
        }
    }
}