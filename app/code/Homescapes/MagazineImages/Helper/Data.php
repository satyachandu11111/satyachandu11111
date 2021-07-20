<?php
namespace Homescapes\MagazineImages\Helper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{	
	protected $_productImageHelper;
	
	public function __construct(
			\Magento\Framework\App\Helper\Context $context,
			\Magento\Store\Model\StoreManagerInterface $storeManager,
			\Magento\Framework\Filesystem $filesystem,
			\Magento\Framework\Image\AdapterFactory $imageFactory
			)
	{
			
			$this->_filesystem = $filesystem;
			$this->_storeManager = $storeManager;
			$this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
			$this->_imageFactory = $imageFactory;
			parent::__construct($context);
	}


	/**
     * Schedule resize of the image
     * $width *or* $height can be null - in this case, lacking dimension will be calculated.
     *
     * @see \Magento\Catalog\Model\Product\Image
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function getMagazinImage($image)
    {
    	$dire="catalog/product";
		$absPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath().$dire.$image;
		if(file_exists($absPath))
		{
			return $resizedURL= $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$dire.$image;
		}else{
			return '';
		}
	}
}
