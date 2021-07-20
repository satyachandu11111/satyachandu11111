<?php
namespace Bss\ProductImagesByCustomer\Plugin;
use \Magento\Store\Model\StoreManagerInterface;
class ModifyExportPlugin
{
   
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        StoreManagerInterface $storeManager

    ) {
        $this->logger             = $logger;
        $this->storeManager = $storeManager;
        
    }
    public function afterGetRowData($subject, $result, $document, $fields, $options)
    {
        $i = 0;
        $mediaRelativePath = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $mediaRelativePath.="bss/productimagesbycustomer/";     
        foreach ($fields as $column) {
            if ($column === 'link_image') {
                $imageUrl =$mediaRelativePath.  $document['link_image'];
                //$imagePath = .$item['link_image'];
                if ($imageUrl) {
                    $result[$i] = $imageUrl;
                }
                //break;
            }
			
			$i++;
            continue;
        }
        return $result;
    }
   
	
}
