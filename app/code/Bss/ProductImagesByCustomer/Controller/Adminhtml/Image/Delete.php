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

class Delete extends \Magento\Backend\App\Action
{
    /**
     * ImageFactory
     *
     * @var ImageFactory
     */
    protected $imageFactory;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param ImageFactory $imageFactory
     */
    public function __construct(
        Action\Context $context,
        ImageFactory $imageFactory
    ) {
        $this->imageFactory = $imageFactory;
        parent::__construct($context);
    }

    /**
     * Delete Image
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('bss_image_customer_upload_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                // init model and delete
                $model = $this->imageFactory->create();
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(
                    __('The image has been deleted.')
                );
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['bss_image_customer_upload_id' => $id]
                );
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(
            __('We can\'t find a size chart to delete.')
        );
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check Rule
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed("Bss_ProductImagesByCustomer::delete");
    }
}
