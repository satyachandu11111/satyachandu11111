<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


declare(strict_types=1);

namespace Amasty\Xnotif\Controller\Unsubscribe;

use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\ProductAlert\Controller\Unsubscribe as UnsubscribeController;

class Stock extends UnsubscribeController
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|null
     */
    private $storeManager;

    /**
     * @var \Magento\ProductAlert\Model\StockFactory|null
     */
    private $stockFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\ProductAlert\Model\StockFactory $stockFactory
    ) {
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->stockFactory = $stockFactory;
        parent::__construct($context, $customerSession);
    }

    /**
     * Unsubscribing from 'back in stock alert'.
     *
     * @return Redirect
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('product_id', $this->getRequest()->getParam('product'));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$productId) {
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }

        try {
            $product = $this->productRepository->getById($productId);
            if (!$product->isVisibleInCatalog()) {
                $this->messageManager->addErrorMessage(__('The product was not found.'));
                $resultRedirect->setPath('customer/account/');
                return $resultRedirect;
            }

            $subscription = $this->stockFactory->create()
                ->setCustomerId($this->customerSession->getCustomerId())
                ->setProductId($product->getId())
                ->setWebsiteId(
                    $this->storeManager
                        ->getStore()
                        ->getWebsiteId()
                )->setStoreId(
                    $this->storeManager
                        ->getStore()
                        ->getId()
                )
                ->loadByParam();
            if ($subscription->getId()) {
                $subscription->delete();
            }
            $this->messageManager->addSuccessMessage(__('You will no longer receive stock alerts for this product.'));
        } catch (NoSuchEntityException $noEntityException) {
            $this->messageManager->addErrorMessage(__('The product was not found.'));
            $resultRedirect->setPath('customer/account/');
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __("The alert subscription couldn't update at this time. Please try again later.")
            );
        }
        $resultRedirect->setUrl($product->getProductUrl());
        return $resultRedirect;
    }
}
