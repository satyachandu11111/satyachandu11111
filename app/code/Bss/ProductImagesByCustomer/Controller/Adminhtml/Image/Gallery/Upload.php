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
namespace Bss\ProductImagesByCustomer\Controller\Adminhtml\Image\Gallery;

use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;

class Upload extends \Magento\Backend\App\Action
{
    /**
     * File system
     *
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * File Uploader Factory
     *
     * @var UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * Raw Factory
     *
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * Adapter Factory
     *
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $adapterFactory;

    /**
     * Store Manager Interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Upload constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Image\AdapterFactory $adapterFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param UploaderFactory $fileUploaderFactory
     * @param Filesystem $filesystem
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UploaderFactory $fileUploaderFactory,
        Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->adapterFactory = $adapterFactory;
        $this->storeManager = $storeManager;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->fileSystem = $filesystem;
    }

    /**
     * Execute
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        try {

            $uploader = $this->fileUploaderFactory->create(['fileId' => 'image']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $uploader->setAllowCreateFolders(true);
            /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
            $imageAdapter = $this->adapterFactory->create();
            $uploader->addValidateCallback('catalog_product_image', $imageAdapter, 'validateUploadFile');
            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */

            $mediaDirectory = $this->fileSystem->getDirectoryRead(
                DirectoryList::MEDIA
            );

            $result = $uploader->save($mediaDirectory->getAbsolutePath('bss/productimagesbycustomer/'));

            unset($result['tmp_name']);
            unset($result['path']);

            $result['url'] = $this->storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'bss/productimagesbycustomer/' . ltrim(str_replace('\\', '/', $result['file']), '/');

        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }

    /**
     * Check Rule
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed("Bss_ProductImagesByCustomer::save");
    }
}
