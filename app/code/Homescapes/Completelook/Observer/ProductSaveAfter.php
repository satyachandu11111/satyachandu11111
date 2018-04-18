<?php
namespace Homescapes\Completelook\Observer;

use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer as EventObserver;

class ProductSaveAfter implements ObserverInterface
{
    
    protected $_request;
    
    protected $_completelook;
    
    protected $_resourceCollection;
    
    private $_messageManager;



    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Homescapes\Completelook\Model\Completelook $completelook,
        \Magento\Framework\App\ResourceConnection $resourceCollection,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->_messageManager = $messageManager;
        $this->_completelook = $completelook;
        $this->_request = $request;
        $this->_resourceCollection = $resourceCollection;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();
        if (!$product) {
            return;
        }
        
        
        $params = $this->_request->getParam('product');
        if(!array_key_exists("complete_look",$params)){ return $this;  }
        $model = $this->_completelook;
        $this->saveProducts($product, $params);
        return $this;
        
//        echo "<pre/>";
//        print_r($params);
//        echo "<pre/>";
//        print_r($completelookProducts);
//        die('haaas');
        
    }
    
    public function saveProducts($product, $params)
    {
        $currentProductId = $product->getId();        
        $completelookProducts = $params['complete_look'];
        $completelookProducts = json_decode($completelookProducts, true);
        unset($completelookProducts['on']);
        $completelookProducts = array_keys($completelookProducts);
        
        try {
        $oldProducts = (array) $this->getProducts($currentProductId);
        $newProducts = (array) $completelookProducts;
        
        $insert = array_diff($newProducts, $oldProducts);
        $delete = array_diff($oldProducts, $newProducts);
        
        $connection = $this->_resourceCollection->getConnection();
        $tableName = $this->_resourceCollection->getTableName(\Homescapes\Completelook\Model\Completelook::COMPLETE_LOOK_PRODUCT);
        
        echo "<pre/>";
        print_r($insert);
        echo "<pre/>";
        print_r($delete);
        
        
        if(count($delete)){
            $where = ['product_id = ?' => (int)$currentProductId, 'look_product_id IN (?)' => $delete];                    
                    $connection->delete($tableName, $where);
        }
        
        
        if(count($insert)){
            $data = [];
                    foreach ($insert as $product_id) {
                        $data[] = ['product_id' => (int)$currentProductId, 'look_product_id' => (int)$product_id];
                    }
                    $connection->insertMultiple($tableName, $data);
        }
        
        }catch (Exception $e) {
                $this->_messageManager->addException($e, __('Something went wrong while saving the contact.'));
            }
        
        
    }
    
    public function getProducts($currentProductId){
        
        $oldProducts = array();
        $connection = $this->_resourceCollection->getConnection();
        $tableName = $this->_resourceCollection->getTableName(\Homescapes\Completelook\Model\Completelook::COMPLETE_LOOK_PRODUCT);
        $sql = $connection->select()
                  ->from($tableName,array('ids' => new \Zend_Db_Expr('GROUP_CONCAT(look_product_id)')))                  
                  ->where('product_id = ?', $currentProductId);
        $results = $connection->fetchCol($sql); 
        $results = reset($results);
        
        $products = explode(',', $results);
        
        
        return $products;
    }
    
    
}