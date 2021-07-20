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

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Bss\ProductImagesByCustomer\Model\ResourceModel\Image\CollectionFactory;
use Bss\ProductImagesByCustomer\Model\Image;
use \Magento\Framework\Exception\LocalizedException;

class MassApprove extends \Magento\Backend\App\Action
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
     * Model
     * @var Image
     */
    protected $model;

    /**
     * MassApprove constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Image $model
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Image $model
    ) {
        $this->model = $model;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection=$this->filter->getCollection($this->collectionFactory->create());

        foreach ($collection as $auctionProduct) {
            $this->setApproveImage($auctionProduct);
        }

        $this->messageManager->addSuccessMessage(
            __(
                'A total of %1 record(s) have been approved.',
                $collection->getSize()
            ));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Set Enable Image
     * @param Image $auctionProduct
     */
    protected function setApproveImage($auctionProduct)
    {
        try {
            $this->model->load($auctionProduct->getId());
            $this->model->setApprove("1");
            $this->model->save();
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
        return $this->_authorization->isAllowed("Bss_ProductImagesByCustomer::save");
    }
}
