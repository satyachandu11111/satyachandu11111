<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Controller\Category;

use Amasty\Faq\Api\CategoryRepositoryInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\Session\Generic as Session;

class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $repository;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var Session
     */
    private $faqSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * View constructor.
     *
     * @param Context                             $context
     * @param CategoryRepositoryInterface         $repository
     * @param Registry                            $coreRegistry
     * @param Session                             $faqSession
     * @param \Magento\Framework\App\Http\Context $httpContext
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $repository,
        Registry $coreRegistry,
        Session $faqSession,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->coreRegistry = $coreRegistry;
        $this->faqSession = $faqSession;
        $this->httpContext = $httpContext;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $category = $this->repository->getById($this->_request->getParam('id'));
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        }
        if (!$category || $category->getStatus() == \Amasty\Faq\Model\OptionSource\Category\Status::STATUS_DISABLED) {
            return $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        }
        $this->coreRegistry->register('current_faq_category', $category);
        $this->coreRegistry->register('current_faq_category_id', $category->getCategoryId());
        $this->faqSession->setLastVisitedFaqCategoryId($category->getCategoryId());
        $this->httpContext->setValue(
            \Amasty\Faq\Model\Context::CONTEXT_CATEGORY,
            $category->getCategoryId(),
            0
        );

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
