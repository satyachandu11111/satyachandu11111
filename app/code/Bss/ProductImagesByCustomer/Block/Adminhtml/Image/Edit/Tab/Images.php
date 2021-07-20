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
namespace Bss\ProductImagesByCustomer\Block\Adminhtml\Image\Edit\Tab;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;

class Images extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content
{

    /**
     * Data Helper
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $dataHelper;

    /**
     * Image Helper
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * Core register
     * @var \Magento\Framework\Registry $coreRegistry
     */
    protected $coreRegistry;

    /**
     * Image Collection Factory
     * @var \Bss\ProductImagesByCustomer\Model\ResourceModel\Image\CollectionFactory $imageCollectionFactory
     */
    protected $imageCollectionFactory;

    /**
     * Store Manager
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * Images constructor.
     * @param \Magento\Framework\Json\Helper\Data $dataHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Bss\ProductImagesByCustomer\Model\ResourceModel\Image\CollectionFactory $imageCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $dataHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Framework\Registry $coreRegistry,
        \Bss\ProductImagesByCustomer\Model\ResourceModel\Image\CollectionFactory $imageCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $jsonEncoder, $mediaConfig, $data);
        $this->imageHelper = $imageHelper;
        $this->coreRegistry = $coreRegistry;
        $this->dataHelper = $dataHelper;
        $this->imageCollectionFactory = $imageCollectionFactory;
        $this->storeManager = $context->getStoreManager();
    }

    /**
     * Get Images Json
     * @return string
     */
    public function getImagesJson()
    {
        $images = $this->getImagesData();
        if (!empty($images)) {
            $mediaDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);

            foreach ($images as &$image) {
                $image['url'] = $this->storeManager->getStore()->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                    ) . '/bss/productimagesbycustomer/' . ltrim(str_replace('\\', '/', $image['file']), '/');
                try {
                    $fileHandler = $mediaDir->stat('/bss/productimagesbycustomer/' . $image['file']);
                    $image['size'] = $fileHandler['size'];
                } catch (FileSystemException $e) {
                    $image['url'] = $this->getImageHelper()->getDefaultPlaceholderUrl('small_image');
                    $image['size'] = 0;
                    $this->_logger->warning($e);
                }

            }
            
            return $this->_jsonEncoder->encode($images);
                
        }

        return '[]';
    }

    /**
     * Get Data Helper
     * @return \Magento\Framework\Json\Helper\Data
     */
    public function getDataHelper()
    {
        return $this->dataHelper;
    }

    /**
     * Get Image Helper
     * @return \Magento\Catalog\Helper\Image
     */
    protected function getImageHelper()
    {
        return $this->imageHelper;
    }

    /**
     * Get Image Types
     *
     * @return array
     */
    public function getImageTypes()
    {
        $imageTypes = [];

        $imageTypes['image'] = [
            'code' => 'image',
            'value' => '',
            'label' => 'Image',
            'scope' => __('GLOBAL'),
            'name' => 'image',
        ];

        return $imageTypes;
    }

    /**
     * Prepare Layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $result = parent::_prepareLayout();
        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->addSessionParam()->getUrl('bss_productimagesbycustomer_admin/image_gallery/upload')
        );
        return $result;
    }

    /**
     * Get images data.
     *
     * @return array
     */
    protected function getImagesData()
    {
        $caseStudyId = $this->coreRegistry->registry('image')->getId();

        $images = $this->imageCollectionFactory->create()
            ->addFieldToFilter('bss_image_customer_upload_id', $caseStudyId);
        $imagesData = [];
        foreach ($images as $image) {
            $imagesData[] = [
                'value_id' => $image->getIdImage(),
                'file' => $image->getLinkImage(),
                'media_type' => 'image'
            ];
        }

        return $imagesData;
    }
}
