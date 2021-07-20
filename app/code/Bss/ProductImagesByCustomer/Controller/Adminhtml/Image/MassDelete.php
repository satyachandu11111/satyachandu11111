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

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Bss\ProductImagesByCustomer\Model\ResourceModel\Image\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Bss\ProductImagesByCustomer\Model\Image;
use \Magento\Framework\Exception\LocalizedException;

class MassDelete  extends \Magento\Backend\App\Action
{

    /**
     * Filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * CollectionFactory
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Image
     * @var Image
     */
    protected $model;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param Image $model
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        Image $model,
        CollectionFactory $collectionFactory
    ) {
        $this->model = $model;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Mass Delete Images
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {

        $collection=$this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach ($collection as $auctionProduct) {
            $this->deleteImage($auctionProduct);
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.',
                $collectionSize)
        );
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Delete Image
     *
     * @param Image $auctionProduct
     * @return void
     */
    protected function deleteImage($auctionProduct)
    {

        try {
            $this->model->load($auctionProduct->getId());
            $this->model->delete();
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __($e->getMessage()));
        }
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
