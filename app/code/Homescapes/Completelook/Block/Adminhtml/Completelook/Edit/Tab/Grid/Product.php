<?php

namespace Homescapes\Completelook\Block\Adminhtml\Completelook\Edit\Tab\Grid;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended as GridExtended;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Catalog\Model\Product\AttributeSet\Options;
use \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttributeSetCollectionFactory;
use \Magento\Framework\App\ResourceConnection;
use \Magento\Catalog\Model\Product\Type as productType;

class Product extends GridExtended
{

    /**
     * @var CustomerGroups
     */
    private $productGroups;

   
    /**
     * @var BannerCollection
     */
    private $productCollection;

   

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;
    
    private $attributeSetOpt;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;
    
    
    protected $_resourceCollection;
    
    protected $_productType;

    /**
     * @param Context $context
     * @param Data $backendHelper     
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataPersistorInterface $dataPersistor
     * @param DataObjectFactory $dataObjectFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Data $backendHelper,        
        ProductCollection $productCollection,
        DataObjectProcessor $dataObjectProcessor,
        DataPersistorInterface $dataPersistor,
        DataObjectFactory $dataObjectFactory,
        Options $attributeSetOpt,        
        ResourceConnection $resourceCollection,
        productType $productType,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->productCollection = $productCollection;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataPersistor = $dataPersistor;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->attributeSetOpt = $attributeSetOpt;
        $this->_resourceCollection = $resourceCollection;
        $this->_productType = $productType;
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    protected function _construct()
    {

        parent::_construct();
        $this->setId('product_list');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(array('in_product' => 1));
        }
    }

    /**
     * Retrieve position for selected products in grid
     *
     * @return array
     */
    public function getSelectedProductPosition()
    {
        $selectedProducts = array();
        $productId = $this->getRequest()->getParam('id');
        $connection = $this->_resourceCollection->getConnection();
        $tableName = $this->_resourceCollection->getTableName(\Homescapes\Completelook\Model\Completelook::COMPLETE_LOOK_PRODUCT);
        $sql = $connection->select()
                  ->from($tableName,array('look_product_id','position' ))                  
                  ->where('product_id = ?', $productId);
        $results = $connection->fetchAll($sql); 
        $products = array();
        foreach($results as $result){
            $products[$result['look_product_id']] = $result['position'];
        }
        
        $selectedProducts = json_encode($products);
        
        return $selectedProducts;
    }
    
    public function _getSelectedProduct()
    {
        $selectedProducts = array();
        $productId = $this->getRequest()->getParam('id');
        $connection = $this->_resourceCollection->getConnection();
        $tableName = $this->_resourceCollection->getTableName(\Homescapes\Completelook\Model\Completelook::COMPLETE_LOOK_PRODUCT);
        $sql = $connection->select()
                  ->from($tableName,array('ids' => new \Zend_Db_Expr('GROUP_CONCAT(look_product_id)')))                  
                  ->where('product_id = ?', $productId);
        $results = $connection->fetchCol($sql); 
        $results = reset($results);
        
        $products = explode(',', $results);
        
        
        return $products;
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('completelook/products/grid', ['_current' => true]);
    }

    /**
     * {@inheritdoc}
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_product') {
            $productIds = $this->_getSelectedProduct();

            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $pId = $this->getRequest()->getParam('id');
        
        $collection = $this->productCollection->create();
        $collection->addAttributeToSelect('name');        
        $collection->addFieldToFilter('entity_id', array('nin' => $pId));
        
        $joinTable = $this->_resourceCollection->getTableName(\Homescapes\Completelook\Model\Completelook::COMPLETE_LOOK_PRODUCT);
        //$collection->getSelect()->joinLeft($joinTable.' as completelook','e.entity_id = completelook.look_product_id', array('completelook.position'));
        //$collection->getSelect()->where('completelook.product_id = ?', $pId);
        //echo $collection->getSelect(); die('dddd');
        
        $query = "select t.position,t.look_product_id from  completelook_product t where t.product_id =$pId";
        $collection->getSelect()->joinLeft(
            new \Zend_Db_Expr('('.$query.')'),
            'e.entity_id = t.look_product_id',
            array('position')    
        );
        //echo  $collection->getSelect(); die('dddd'); 

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
       
        $this->addColumn(
            'in_product',
            [
                'type' => 'checkbox',
                'name' => 'in_product',
                'values' => $this->_getSelectedProduct(),
                'index' => 'entity_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );
        
        
           $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'index' => 'entity_id',
                
            ]
        );
       
           $this->addColumn(
            'name',
            [
                'header' => __('Product Name'),
                'index' => 'name',
                
            ]
        );
           $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        
           $options = array();
           $sets = $this->attributeSetOpt->toOptionArray();
           $options[0] = '---select---';
           foreach($sets as $set){
                $options[$set['value']] = $set['label'];
           }

           
         $this->addColumn(
            'set_name',
            [
                'header' => __('Attribute Set'),
                'index' => 'attribute_set_id',
                'type' => 'options',
                'options' => $options,
                'header_css_class' => 'col-attr-name',
                'column_css_class' => 'col-attr-name'
            ]
        );
         
        $optionsTypes = array();         
        $optionsTypes = $this->_productType->getOptionArray(); 
         
        $this->addColumn(
            'type_id',
            [
                'header' => __('Type'),
                'index' => 'type_id',
                'type' => 'options',
                'options' => $optionsTypes,
                'header_css_class' => 'col-attr-name',
                'column_css_class' => 'col-attr-name'
            ]
        ); 
        
        

        $this->addColumn(
                'position', [
            'header' => __('Position'),            
            'index' => 'position',
            'type' => 'input',             
            'filter' => false
            //'renderer' => 'Homescapes\Completelook\Block\Adminhtml\Completelook\Edit\Tab\Grid\Position'
                ]
        );


              
      
        return parent::_prepareColumns();
    }

    
    
}

