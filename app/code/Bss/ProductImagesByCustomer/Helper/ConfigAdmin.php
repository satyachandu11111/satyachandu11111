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

use \Magento\Framework\App\Helper\AbstractHelper;

class ConfigAdmin extends AbstractHelper
{

    /**
     * ScopeConfigInterface
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * ConfigAdmin constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
    }

    //General Config

    /**
     * Get Config enable module
     *
     * @return string
     */
    public function configEnableModule()
    {
        return $this->scopeConfig->getValue(
            'bss_product_image_by_customer/bss_product_image_by_customer_general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Config allow guest don't login upload image
     * @return string
     */
    public function configGuestUploadImage()
    {
        return $this->scopeConfig->getValue(
            'bss_product_image_by_customer/bss_product_image_by_customer_general/allow_not_login',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Config email required upload image
     * @return string
     */
    public function configEmailRequired()
    {
        return $this->scopeConfig->getValue(
            'bss_product_image_by_customer/bss_product_image_by_customer_general/email_require',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Config title tab
     * @return string
     */
    public function configTitleTab()
    {
        return $this->scopeConfig->getValue(
            'bss_product_image_by_customer/bss_product_image_by_customer_general/title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Config Description Tab
     * @return string
     */
    public function configDescriptionTab()
    {
        return $this->scopeConfig->getValue(
            'bss_product_image_by_customer/bss_product_image_by_customer_general/description',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    //Images setting

    /**
     * Config speed slider
     * @return int
     */
    public function configSpeedSlider()
    {
        return $this->scopeConfig->getValue(
            'bss_product_image_by_customer/bss_product_image_by_customer_image_setting/speed_slide',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Config image per slide
     * @return int
     */
    public function configImagePerSlide()
    {
        return $this->scopeConfig->getValue(
            'bss_product_image_by_customer/bss_product_image_by_customer_image_setting/number_image_per_slide',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Config number upload images upload once
     *
     * @return int
     */
    public function configNumberUploadImageUploadOnce()
    {
        return $this->scopeConfig->getValue(
            'bss_product_image_by_customer/bss_product_image_by_customer_image_setting/number_image_upload_once',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Config Horizontal Image In Slide
     * @return int
     */
    public function configHorizontalImageInSlide()
    {
        return $this->scopeConfig->getValue(
            'bss_product_image_by_customer/bss_product_image_by_customer_image_setting/slide_horizontal_image_dimension',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Config Vertical Image In Slide
     * @return int
     */
    public function configVerticalImageInSlide()
    {
        return $this->scopeConfig->getValue(
            'bss_product_image_by_customer/bss_product_image_by_customer_image_setting/slide_vertical_image_dimension',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Config Horizontal Image Click
     * @return int
     */
    public function configHorizontalImageClick()
    {
        return $this->scopeConfig->getValue(
            'bss_product_image_by_customer/bss_product_image_by_customer_image_setting/click_horizontal_image_dimension',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Config Vertical Image Click
     * @return int
     */
    public function configVerticalImageClick()
    {
        return $this->scopeConfig->getValue(
            'bss_product_image_by_customer/bss_product_image_by_customer_image_setting/click_vertical_image_dimension',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    //Email setting

    /**
     * Config enable email
     * @return int
     */
    public function configEnableEmail()
    {
        return $this->scopeConfig->getValue(
            'bss_product_image_by_customer/bss_product_image_by_customer_email_notification/enable_email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Config sender email
     * @return string
     */
    public function configSenderEmail()
    {
        return $this->scopeConfig->getValue(
            'bss_product_image_by_customer/bss_product_image_by_customer_email_notification/email_sender',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Config email receiver
     * @return string
     */
    public function configEmailReceiver()
    {
        return $this->scopeConfig->getValue(
            'bss_product_image_by_customer/bss_product_image_by_customer_email_notification/email_receiver',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Config email template
     * @return string
     */
    public function configEmailTemplate()
    {
        return $this->scopeConfig->getValue(
            'bss_product_image_by_customer/bss_product_image_by_customer_email_notification/email_template',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
