<?php

namespace Homescapes\Fbpixel\Helper;    

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_categoryFactory;    

    public function __construct(    	
    \Magento\Catalog\Model\CategoryFactory $categoryFactory    
    ) {

        $this->_categoryFactory = $categoryFactory;
    }

	public function getParentCategoryName($category)
	{
		$parentCategoryId = $category->getParentId();
		
		if($parentCategoryId && $parentCategoryId > 2){
	        $parentCategory = $this->_categoryFactory->create()->load($parentCategoryId);
	        $categoryName = $parentCategory->getName();
	        return $categoryName;
	    }
		return $category->getName();
	}

}