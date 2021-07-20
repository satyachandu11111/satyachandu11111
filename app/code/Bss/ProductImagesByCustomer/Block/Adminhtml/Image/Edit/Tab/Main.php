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

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Catalog\Model\ProductFactory;
use \Magento\Framework\App\ProductMetadataInterface;

/**
 * Image edit form main tab
 */
class Main extends Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * Store
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * Version Magento
     * @var string
     */
    protected $version;

    /**
     * Yesno
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $status;

    /**
     * Product Factory
     * @var \Magento\Catalog\Model\ProductFactory;
     */
    protected $productFactory;

    /**
     * Main constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        ProductMetadataInterface $version,
        ProductFactory $productFactory,
        array $data = []
    ) {
        $this->version = $version;
        $this->systemStore = $systemStore;
        $this->status = $yesno;
        $this->productFactory = $productFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return \Magento\Backend\Block\Widget\Form\Generic
     */
    protected function _prepareForm()
    {
        /* @var $model \Bss\ProductImagesByCustomer\Model\Image */
        $model = $this->_coreRegistry->registry('image');

        $isElementDisabled = false;

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Edit Image')]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'bss_image_customer_upload_id',
                'hidden', ['name' => 'bss_image_customer_upload_id']
            );
        }

        $fieldset->addField(
            'approve',
            'select',
            [
                'label' => __('Approved'),
                'title' => __('Approved'),
                'name' => 'approve',
                'required' => true,
                'options' => $this->status->toArray(),
                'disabled' => $isElementDisabled
            ]
        );
        if (!$model->getId()) {
            $model->setData('enable', $isElementDisabled ? '0' : '1');
        }

        $fieldset->addField(
            'email_customer',
            'text',
            [
                'label' => __('Customer Email'),
                'title' => __('Customer Email'),
                'name' => 'email_customer',
                'required' => true,
                'disabled' => $this->checkAddOrEdit()
            ]
        );

        $fieldset->addField(
            'customer_name',
            'text',
            [
                'label' => __('Customer Name'),
                'title' => __('Customer Name'),
                'name' => 'customer_name',
                'required' => true,
                'disabled' => $this->checkAddOrEdit()
            ]
        );


            $fieldset->addField(
                'images',
                'Bss\ProductImagesByCustomer\Data\Form\Element\Images',
                [
                    'label' => __('Image'),
                    'title' => __('Image'),
                    'name' => 'link_images',
                    'note' => 'Allowed types of image: jpg, jpeg, gif, png',
                    'disable' => true,
                    'required' => true
                ]
            );

        $fieldset->addField(
            'id_product',
            'text',
            [
                'label' => __('SKU'),
                'title' => __('SKU'),
                'name' => 'id_product',
                'required' => false,
                'note' => "Please enter a product SKU to apply images. This module doesn't support assigning to multiple products at once",
                'disabled' => $this->checkAddOrEdit()
            ]
        );


        $fieldset->addField(
            'customer_date',
            'date',
            [
                'label' => __('Customer Date'),
                'title' => __('Customer Date'),
                'name' => 'customer_date',
                'required' => false,
                'note' => "Please enter a Customer Date to apply images.",
                'date_format' => 'yyyy-MM-dd',
                'disabled' => $this->checkAddOrEdit()
            ]
        );


        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'id_store',
                'multiselect',
                [
                    'name' => 'id_store',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->systemStore->getStoreValuesForForm(false, true)
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'id_store',
                'hidden',
                [
                    'name'=>'id_store',
                    'value'=>$this->_storeManager->getStore(true)->getId()
                ]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }
        if (isset($model) && $model->getIdImage() != 0) {
            $urlOld = $model->getLinkImage();
            $urlNew = "/bss/productimagesbycustomer/".$urlOld;
            $model->setLinkImage($urlNew);
        }
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Check param  bss_image_customer_upload_id on URL if isset return true else return false
     * @return bool
     */
    protected function checkAddOrEdit()
    {
        $idImage = $this->getRequest()->getParam("bss_image_customer_upload_id");
        if (isset($idImage)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Name Product By Id
     * @param int $idProduct
     * @return string
     */
    protected function getProductNameById($idProduct)
    {
        $product = $this->productFactory->create()->load($idProduct);
        return $product->getName();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Edit Image');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Edit Image');
    }

    /**
     * Can Show Tab
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is Hidden
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Get magento version
     * @return string
     */
    protected function getMagentoVersion()
    {
        return $this->version->getVersion();
    }

    /**
     * Conpare Version
     * @param $version
     * @return bool
     */
    protected function compareVersion($version)
    {
        $versionCurrent = $this->getMagentoVersion();
        if (version_compare($versionCurrent, $version) === 1) {
            return true;
        } else {
            return false;
        }
    }
}
