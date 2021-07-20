<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ProductImagesByCustomer
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductImagesByCustomer\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Resize extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Custom directory relative to the "media" folder
     */
    const DIRECTORY = 'bss/productimagesbycustomer';

    /**
     * WriteInterface
     * @var \Magento\Framework\Filesystem
     */
    protected $mediaDirectory;

    /**
     * ImageFactory
     * @var \Magento\Framework\Image\Factory
     */
    protected $imageFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Image constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->mediaDirectory = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * First check this file on FS
     *
     * @param string $filename
     * @return bool
     */
    protected function fileExists($filename)
    {
        if ($this->mediaDirectory->isFile($filename)) {
            return true;
        }
        return false;
    }

    /**
     * Resize
     * @param $image
     * @param int $width
     * @param int $height
     * @return string
     */
    public function resize($image, $width = null, $height = null)
    {
        $mediaFolder = self::DIRECTORY;

        $mediaDirectory = $this->mediaDirectory->getDirectoryWrite(DirectoryList::MEDIA);
        $path = 'tmp/' . $mediaFolder . '/image';

        if ($width !== null) {
            $path .= '/' . $width . 'x';
            if ($height !== null) {
                $path .= $height;
            }
        }

        $absolutePath = $mediaDirectory->getAbsolutePath() . $image;
        $imageResized = $mediaDirectory->getAbsolutePath($path) . $image;
        $filename = $path . $image;
        //Constrain will expland image
        if (!$mediaDirectory->isFile($filename)) {
            $imageResize = $this->imageFactory->create();
            $imageResize->open($absolutePath);
            $imageResize->constrainOnly(false);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(true);
            $imageResize->keepAspectRatio(true);
            $imageResize->backgroundColor([255, 255, 255]);
            $imageResize->resize($width, $height);
            $imageResize->save($imageResized);
        }
        $resizedURL = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$filename;
        return $resizedURL;
    }
}
