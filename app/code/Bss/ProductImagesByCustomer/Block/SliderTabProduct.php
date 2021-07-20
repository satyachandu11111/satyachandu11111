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
namespace Bss\ProductImagesByCustomer\Block;

use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Bss\ProductImagesByCustomer;
use Magento\Framework\Data\Form\FormKey;
use Bss\ProductImagesByCustomer\Helper\ConfigAdmin;
use Bss\ProductImagesByCustomer\Model\ResourceModel;
use Bss\ProductImagesByCustomer\Helper\Resize;

class SliderTabProduct extends Template
{
    /**
     * To second from Micro second
     */
    const CHANGETIME = 1000;

    /**
     * IMAGE_IN_SLIDER
     */
    const IMAGE_IN_SLIDER = 1;

    /**
     * IMAGE_IN_MODAL
     */
    const IMAGE_IN_MODAL = 2;

    /**
     * Asset Repo
     * @var  \Magento\Framework\View\Asset\Repository $assetRepo
     */
    protected $assetRepo;

    /**
     * Store Manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Resource Model Image
     * @var ResourceModel\Image
     */
    protected $resourceImage;

    /**
     * Helper config admin
     * @var ConfigAdmin
     */
    protected $helperConfigAdmin;

    /**
     * Form Key
     * @var FormKey
     */
    protected $formKey;
    /**
     * Customer Login
     * @var ProductImagesByCustomer\Helper\CustomerLogin $helperCustomerLogin
     */
    protected $helperCustomerLogin;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Json Encoder
     *
     * @var \Magento\Framework\Json\Helper\Data $jsonEncoder
     */
    protected $jsonEncoder;

    /**
     * Helper Resize
     * @var Resize $helperResize
     */
    protected $helperResize;

    /**
     * SliderTabProduct constructor.
     *
     * @param Template\Context $context
     * @param Registry $registry
     * @param ProductImagesByCustomer\Helper\CustomerLogin $helperCustomerLogin
     * @param FormKey $formKey
     * @param ConfigAdmin $helperConfigAdmin
     * @param ResourceModel\Image $resourceImage
     * @param \Magento\Framework\Json\Helper\Data $jsonEncoder
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        ProductImagesByCustomer\Helper\CustomerLogin $helperCustomerLogin,
        FormKey $formKey,
        ConfigAdmin $helperConfigAdmin,
        ResourceModel\Image $resourceImage,
        \Magento\Framework\Json\Helper\Data $jsonEncoder,
        Resize $helperResize,
        array $data
    ) {
        $this->helperResize = $helperResize;
        $this->jsonEncoder = $jsonEncoder;
        $this->storeManager = $context->getStoreManager();
        $this->formKey = $formKey;
        $this->resourceImage = $resourceImage;
        $this->helperCustomerLogin = $helperCustomerLogin;
        $this->coreRegistry = $registry;
        $this->helperConfigAdmin = $helperConfigAdmin;
        $this->assetRepo = $context->getAssetRepository();
        parent::__construct($context, $data);
    }

    /**
     * Get Width Height Resize Slider
     * @return array
     */
    protected function getWidthHeightResizeSlider()
    {
        $width = $this->helperConfigAdmin->configHorizontalImageInSlide();
        $height = $this->helperConfigAdmin->configVerticalImageInSlide();
        $arr = [
            'width' => $width,
            'height' => $height
        ];
        return $arr;
    }

    /**
     * Get Width Height Resize Modal
     * @return array
     */
    protected function getWidthHeightResizeModal()
    {
        $width = $this->helperConfigAdmin->configHorizontalImageClick();
        $height = $this->helperConfigAdmin->configVerticalImageClick();
        $arr = [
            'width' => $width,
            'height' => $height
        ];
        return $arr;
    }

    /**
     * Resize Image
     * @param string $linkImage
     * @param int $option
     * @return string
     */
    protected function resizeImage($linkImage, $option)
    {
        $arr = [];
        if ($option == self::IMAGE_IN_SLIDER) {
            $arr = $this->getWidthHeightResizeSlider();
        } elseif ($option == self::IMAGE_IN_MODAL) {
            $arr = $this->getWidthHeightResizeModal();
        }
        $linkImage = $this->helperResize->resize($linkImage,
            $arr['width'],
            $arr['height']
        );
        return $linkImage;
    }

    /**
     * Check Customer Logined
     * @return bool
     */
    public function checkCustomerLogined()
    {
        return $this->helperCustomerLogin->checkCustomerLogined();
    }

    /**
     * Get Form Key
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * Get helper config admin
     * @return ConfigAdmin
     */
    public function getHelperConfigAdmin()
    {
        return $this->helperConfigAdmin;
    }

    /**
     * Get Email Customer
     * @return string
     */
    public function getEmailCustomer()
    {
        return $this->helperCustomerLogin->getEmailCustomer();
    }

    /**
     * Get Product
     * @return Product $ProductFactory
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->coreRegistry->registry('product'));
        }
        $product = $this->getData('product');

        return $product;
    }

    /**
     * Get Store View Id Front End
     * @return int
     */
    public function getStoreViewIdFrontEnd()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Get Images In Slide
     * @return array
     */
    public function getImagesInSlide()
    {
        //Get number images in slide at config admin
        $numberImagesPerSlide = $this->helperConfigAdmin->configImagePerSlide();
        $storeViewId = $this->getStoreViewIdFrontEnd();
        $productCode = $this->getProduct()->getSku();
        $arrImagesDataApprove = $this->resourceImage->getImagesDataBaseByProduct($productCode, $storeViewId);

        $arrImagesDisplayPerSlide = [];
        if (count($arrImagesDataApprove) <= $numberImagesPerSlide) {
            foreach ($arrImagesDataApprove as $images) {
                array_push($arrImagesDisplayPerSlide, $images);
            }
        } else {
            $count = 0;
            foreach ($arrImagesDataApprove as $images) {
                if ($count < $numberImagesPerSlide) {
                    array_push($arrImagesDisplayPerSlide, $images);
                    $count++;
                } else {
                    break;
                }
            }
        }

        return $arrImagesDisplayPerSlide;
    }

    /**
     * Get Icon Slider
     * @param String $nameFile
     * @return string
     * return string
     */
    public function getIconSlider($nameFile)
    {
        return $this->assetRepo->getUrl("Bss_ProductImagesByCustomer::images/{$nameFile}");
    }

    /**
     * Edit Src Image In Slide
     * @param string $linkImage
     * @param int $option
     * @return  string $linkImage
     */
    public function editSrcImageInSlide($linkImage, $option)
    {
        $linkImage = "bss/productimagesbycustomer/".$linkImage ;
        /**
         * Resize images
         */
        $linkImage = $this->resizeImage($linkImage, $option);
        return $linkImage;
    }

    /**
     * Change speed slider to s from ms
     *
     * @return int Speed
     */
    public function changeSpeedSlider()
    {
        $speedSlider = $this->helperConfigAdmin->configSpeedSlider();
        $speedSlider = $speedSlider * self::CHANGETIME;
        return $speedSlider;
    }

    /**
     * Get Upload Max File Size
     *
     * @return int
     */
    public function getUploadMaxFileSize()
    {
        return (int)ini_get('upload_max_filesize');
    }
}
