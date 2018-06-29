<?php

namespace Homescapes\Ratings\Block\Review;

class Form extends \Magento\Review\Block\Form
{
    protected $_registry; 
    
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Url\EncoderInterface $urlEncoder, \Magento\Review\Helper\Data $reviewData, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Review\Model\RatingFactory $ratingFactory, \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\App\Http\Context $httpContext, \Magento\Customer\Model\Url $customerUrl, \Magento\Framework\Registry $registry, array $data = array(), \Magento\Framework\Serialize\Serializer\Json $serializer = null) {
        parent::__construct($context, $urlEncoder, $reviewData, $productRepository, $ratingFactory, $messageManager, $httpContext, $customerUrl, $data, $serializer);        
        $this->_registry = $registry;
    }

    
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Magento_Review::form.phtml');
    }
        public function getProductInfo()
    {
            
            $currentProduct = $this->_registry->registry('current_product');
            $requestProductId = $this->getProductId();
            if($currentProduct->getId()){
                if($currentProduct->getId() != $requestProductId){
                    return $this->productRepository->getById(
                            $currentProduct->getId(),
                            false,
                            $this->_storeManager->getStore()->getId()
                        );
                }else{
                    return parent::getProductInfo();
                }            
            }else{
                return parent::getProductInfo();
            }            
        
    }
    
    
   
    
}
