<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Controller\Question;

use Amasty\Faq\Api\Data\QuestionInterface;
use Amasty\Faq\Api\QuestionRepositoryInterface;
use Amasty\Faq\Model\OptionSource\Question\Status;
use Amasty\Faq\Model\OptionSource\Question\Visibility;
use Amasty\Faq\Model\ResolveQuestionCategory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\App\Http\Context as HttpContext;

class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var ResolveQuestionCategory
     */
    private $resolveQuestionCategory;

    /**
     * @var QuestionRepositoryInterface
     */
    private $repository;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * View constructor.
     *
     * @param Context                     $context
     * @param QuestionRepositoryInterface $repository
     * @param Registry                    $coreRegistry
     * @param Session                     $session
     * @param ResolveQuestionCategory     $resolveQuestionCategory
     * @param HttpContext                 $httpContext
     */
    public function __construct(
        Context $context,
        QuestionRepositoryInterface $repository,
        Registry $coreRegistry,
        Session $session,
        ResolveQuestionCategory $resolveQuestionCategory,
        HttpContext $httpContext
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->resolveQuestionCategory = $resolveQuestionCategory;
        $this->repository = $repository;
        $this->coreRegistry = $coreRegistry;
        $this->httpContext = $httpContext;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $question = $this->repository->getById($this->_request->getParam('id'));
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        }

        if (!$question
            || $question->getStatus() != Status::STATUS_ANSWERED
            || $question->getVisibility() == Visibility::VISIBILITY_NONE
            || (
                $question->getVisibility() == Visibility::VISIBILITY_FOR_LOGGED
                && !$this->session->isLoggedIn()
            )
        ) {
            return $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        }
        $this->coreRegistry->register('current_faq_question', $question);
        $this->coreRegistry->register('current_faq_question_id', $question->getQuestionId());

        $this->setCurrentCategory($question);

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }

    /**
     * @param QuestionInterface $question
     */
    private function setCurrentCategory(QuestionInterface $question)
    {
        $categoryId = $this->resolveQuestionCategory->execute($question);

        $this->httpContext->setValue(
            \Amasty\Faq\Model\Context::CONTEXT_CATEGORY,
            $categoryId,
            0
        );

        $this->coreRegistry->register(
            'current_faq_category_id',
            $categoryId
        );
    }
}
