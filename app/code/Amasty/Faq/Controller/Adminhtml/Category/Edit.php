<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Amasty\Faq\Model\CategoryFactory;
use Amasty\Faq\Api\CategoryRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;

class Edit extends \Amasty\Faq\Controller\Adminhtml\Category
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $repository;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    public function __construct(
        Context $context,
        CategoryFactory $categoryFactory,
        CategoryRepositoryInterface $repository
    ) {
        parent::__construct($context);
        $this->categoryFactory = $categoryFactory;
        $this->repository = $repository;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            try {
                $model = $this->repository->getById($id);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This category no longer exists.'));
                return $this->_redirect('*/*/edit', ['id' => $id]);
            }
        } else {
            $model = $this->categoryFactory->create();
        }

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_Faq::category');
        $resultPage->addBreadcrumb(__('Categories'), __('Categories'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('New Category'));

        return $resultPage;
    }
}
