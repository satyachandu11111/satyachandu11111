<?php

namespace Homescapes\Completelook\Block\Adminhtml\Completelook\Edit\Tab\Grid;

class Position extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    
    protected $_resourceConnection;


    public function __construct(
            \Magento\Backend\Block\Context $context,
            \Magento\Framework\App\ResourceConnection $resourceConnection, 
            array $data = array()) {
        parent::__construct($context, $data);
        $this->_resourceConnection = $resourceConnection;
        
    }

    public function render(\Magento\Framework\DataObject $row){
        //var_dump($row->getData()); die('tttt');
        $position = $row->getData($this->getColumn()->getIndex());
//        var_dump($productID); die('ddddtttt');
//        $mainProductId = $this->getRequest()->getParam('id');
//        $connection = $this->_resourceConnection->getConnection();
//        $tableName = $this->_resourceConnection->getTableName(\Homescapes\Completelook\Model\Completelook::COMPLETE_LOOK_PRODUCT);
//        $sql = $connection->select()
//                  ->from($tableName,array('position' => 'position'))                  
//                  ->where('product_id = ? and look_product_id = ?', $mainProductId,$productID);
//        
//        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
//            $logger = new \Zend\Log\Logger();
//            $logger->addWriter($writer);
//
//            $logger->info($sql);
//          
//        
//        $result = $connection->fetchCol($sql); 
//        $result = reset($result);
        if(!$position){ $position = 0; }
        $html = '<input name="product[complete_look_position]" data-form-part="product_form" type="text" value="'.$position.'" />';
        return $html;
      }
}