<?php

namespace Homescapes\General\Helper;    

use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;

class Image extends \Magento\Framework\App\Helper\AbstractHelper
{

	protected $galleryReadHandler;

    /**
     * Catalog Image Helper
     *
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    protected $_productloader;

    public function __construct(
    \Magento\Catalog\Model\ProductFactory $_productloader,    
    GalleryReadHandler $galleryReadHandler,  \Magento\Framework\App\Helper\Context $context,\Magento\Catalog\Helper\Image $imageHelper)
    {
        $this->_productloader = $_productloader;
        $this->imageHelper = $imageHelper;
        $this->galleryReadHandler = $galleryReadHandler;
        parent::__construct($context);
    }
   

    /* return Hover image and display in listing page */
    public function getHoverImage($_product)
    {

          $ImageUrl = '';  
          $productId = $_product->getId();
          $width = 300;
          $height = 300;

                    
            //$_productat = $this->_productloader->create()->load($productId);  
            $attrImage = $_product->getData('on_hover');   
            if(isset($attrImage) && $attrImage != 'no_selection' ){
                $productImageAttr = $_product->getCustomAttribute('on_hover');
                $productImage = $this->imageHelper->init($_product, 'on_hover')
                                ->resize($width, $height)    
                                ->setImageFile($productImageAttr->getValue());
                $ImageUrl =$productImage->getUrl();
            }

        return $ImageUrl;
    }

}