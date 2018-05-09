<?php

namespace Homescapes\Completelook\Controller\Adminhtml\Import;          


use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem\Io\File;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\ResourceConnection;

class Save extends Action
{
    protected $fileSystem;
 
    protected $uploaderFactory;
 
    protected $allowedExtensions = ['csv']; // to allow file upload types 
 
    protected $fileId = 'import_file'; // name of the input file box  
    
    protected $directoryList;
    
    protected $file;
    
    protected $productFactory;
    
    protected $resourceConnection;

    

    const COMEPLETELOOK_DIR = 'import_completelook';
 
    public function __construct(
        Action\Context $context,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory,
        DirectoryList $directoryList,
        File $file,
        ProductFactory $productFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->directoryList = $directoryList;
        $this->file = $file;        
        $this->productFactory = $productFactory;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }
 
     

    
    public function execute()
    {
        $destinationPath = $this->getDestinationPath();       
        
 
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => $this->fileId])
                ->setAllowCreateFolders(true)
                ->setAllowedExtensions($this->allowedExtensions);
                //->addValidateCallback('validate', $this, 'validateFile');
            
            $temp = explode(".", $_FILES["import_file"]["name"]);
            $newfilename = reset($temp).'-'.date('d-m-Y_H_i_s') . '.' . end($temp);
            
            if (!$uploader->save($destinationPath,$newfilename)) {
                throw new LocalizedException(
                    __('File cannot be saved to path: $1', $destinationPath)
                );
            }
            
           
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __($e->getMessage())
            );
            $this->_redirect('*/*/index');
               return;
        }
        
        
        
        try{            
            
            $csvFile = $this->directoryList->getPath('var').'/'.self::COMEPLETELOOK_DIR.'/'.$newfilename;
            
                    $row = 0;
                    $errorProductsSkus = array();
                    if (($handle = fopen($csvFile, "r")) !== FALSE) {
                            while (($actual_data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                    $num = count($actual_data);
                                    $row++;
                                    $productSku = '';
                                    $completelookSkus = array();
                                    $completelookProductIds = $productIds = array();
                                    $productId = '';
                                    if($row> 1){
                                    $new_data = $newProducts = $oldProducts = array();
                                    $productSku = trim($actual_data[0]);
                                    $completelookSkus = explode(',',trim($actual_data[1]));
                                    
                                    $productId = $this->productFactory->create()->getIdBySku($productSku);
                                    if(!$productId){
                                        $errorProductsSkus[] = $productSku;
                                        continue;
                                    }
                                    
                                    foreach($completelookSkus as $completelookSku){
                                        $subproductsIds = '';
                                        $subproductsIds = $this->productFactory->create()->getIdBySku($completelookSku);                                        
                                        if($subproductsIds && $subproductsIds !=$productId){
                                            $completelookProductIds[] = $subproductsIds;
                                        }else{
                                            if(in_array($productSku, $errorProductsSkus)){
                                                $errorProductsSkus[] = $productSku;
                                            }
                                            
                                        }
                                    }
                                    
                                    $oldProducts = (array) $this->getProducts($productId);
                                    $newProducts = array_diff($completelookProductIds,$oldProducts);
                                    //$deleteProducts = array_diff($oldProducts,$completelookProductIds);
                                        
                                    
                                    $connection = $this->resourceConnection->getConnection();
                                    $tableName = $this->resourceConnection->getTableName(\Homescapes\Completelook\Model\Completelook::COMPLETE_LOOK_PRODUCT);
                                    
                                    if(count($newProducts)){
                                            $data = [];
                                                    foreach ($newProducts as $newProduct) {                        
                                                        $data[] = ['product_id' => (int)$productId, 'look_product_id' => (int)$newProduct];
                                                    }
                                                    $connection->insertMultiple($tableName, $data);
                                        }

                                    
                                    
                                    }	

                            }
                            fclose($handle);
                    }
            
                    if(count($errorProductsSkus)> 0){
                        $errorProductsSkus = implode(',',$errorProductsSkus);
                        $this->messageManager->addError(__('some products "%1" issues for import complete looks',$errorProductsSkus));                        
                    }else{   
                        $this->messageManager->addSuccess(__('Import Complete look producrs successfully saved.'));
                    }
                    
               $this->_redirect('*/*/index');
               return;
            
            
        }catch (\Exception $e) {
            $this->messageManager->addError(
                __($e->getMessage())
            );
            $this->_redirect('*/*/index');
               return;
        }
    }


    public function getProducts($currentProductId){
        
        $oldProducts = array();
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(\Homescapes\Completelook\Model\Completelook::COMPLETE_LOOK_PRODUCT);
        $sql = $connection->select()
                  ->from($tableName,array('ids' => new \Zend_Db_Expr('GROUP_CONCAT(look_product_id)')))                  
                  ->where('product_id = ?', $currentProductId);
        $results = $connection->fetchCol($sql); 
        $results = reset($results);
        
        $products = explode(',', $results);
        
        
        return $products;
    }
    
 
    public function getDestinationPath()
    {
        $path = $this->directoryList->getPath('var').'/'.self::COMEPLETELOOK_DIR;
        //die($path);
        // create folder if it is not already exist
        if (!is_dir($path)) {            
            $ioAdapter = $this->file;
            $ioAdapter->mkdir($path, 0777);        
        }
        return $path;
        
    }
    
}

