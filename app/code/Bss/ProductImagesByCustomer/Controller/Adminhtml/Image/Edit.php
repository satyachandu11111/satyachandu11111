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
namespace Bss\ProductImagesByCustomer\Controller\Adminhtml\Image;

use Magento\Backend\App\Action;
use Bss\ProductImagesByCustomer\Model\ImageFactory;
use Magento\Backend\Model\Session;

class Edit extends \Magento\Backend\App\Action
{

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $coreRegistry = null;

    /**
     * Session
     * @var Session
     */
    protected $session;

    /**
     * PageFactory
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Image Factory
     * @var ImageFactory
     */
    protected $imageFactory;

    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param ImageFactory $imageFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        ImageFactory $imageFactory
    ) {
        $this->session = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->imageFactory = $imageFactory;
        parent::__construct($context);
    }


    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Bss_ProductImagesByCustomer::images')
            ->addBreadcrumb(__('Images'), __('Images '))
            ->addBreadcrumb(
                __('Images Information'),
                __('Images Information')
            );
        return $resultPage;
    }

    /**
     * Edit Image
     *
     * @return  \Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('bss_image_customer_upload_id');
        $model = $this->imageFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(
                    __('This Image no longer exists.')
                );
                /** \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->coreRegistry->register('image', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Image') : __('New Image'),
            $id ? __('Edit Image') : __('New Image')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Image'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Image'));

        return $resultPage;
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
