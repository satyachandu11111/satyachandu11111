<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Block\View;

use Amasty\Faq\Block\AbstractPage;
use Amasty\Faq\Model\ConfigProvider;
use Amasty\Faq\Model\QuestionRepository;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Framework\DataObject\IdentityInterface;

class Question extends Template implements IdentityInterface
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    public function __construct(
        Template\Context $context,
        Registry $coreRegistry,
        ConfigProvider $configProvider,
        QuestionRepository $questionRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->configProvider = $configProvider;
        $this->questionRepository = $questionRepository;
        $this->setData('cache_lifetime', 86400);
    }

    /**
     * @return \Amasty\Faq\Api\Data\QuestionInterface|bool
     */
    public function getCurrentQuestion()
    {
        if ($this->getQuestionId()) {
            try {
                return $this->questionRepository->getById($this->getQuestionId());
            } catch (\Exception $e) {
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getQuestionId()
    {
        if (!$this->hasData('question_id')) {
            $this->setData('question_id', $this->coreRegistry->registry('current_faq_question_id'));
        }

        return (int)$this->getData('question_id');
    }
    
    /**
     * @return bool
     */
    public function showAskQuestionForm()
    {
        if (!$this->hasData('show_ask_form')) {
            $this->setData('show_ask_form', $this->configProvider->isShowAskQuestionOnAnswerPage());
        }

        return (bool)$this->getData('show_ask_form');
    }

    /**
     * Add metadata to page header
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $question = $this->getCurrentQuestion();
        if ($question) {
            $this->pageConfig->getTitle()->set($question->getMetaTitle() ? : __('Question'));
            if ($description = $question->getMetaDescription()) {
                $this->pageConfig->setDescription($description);
            }

            /** @var \Magento\Theme\Block\Html\Title $headingBlock */
            if ($headingBlock = $this->getLayout()->getBlock('page.main.title')) {
                $headingBlock->setPageTitle($question->getTitle());
            }

            if ($this->configProvider->isCanonicalUrlEnabled()) {
                $this->pageConfig->addRemotePageAsset(
                    $this->getCanonicalUrl($question),
                    'canonical',
                    ['attributes' => ['rel' => 'canonical']]
                );
            }

            if ($question->isNoindex() || $question->isNofollow()) {
                if ($question->isNoindex() && $question->isNofollow()) {
                    $this->pageConfig->setRobots('NOINDEX,NOFOLLOW');
                } elseif ($question->isNofollow()) {
                    $this->pageConfig->setRobots('NOFOLLOW');
                } else {
                    $this->pageConfig->setRobots('NOINDEX');
                }
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Amasty\Faq\Model\Question::CACHE_TAG . '_' . $this->getQuestionId()];
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return parent::getCacheKeyInfo() + ['q_id' => $this->getQuestionId()];
    }

    /**
     * Generate canonical url for page
     *
     * @param \Amasty\Faq\Model\Question $question
     * @return string
     */
    public function getCanonicalUrl(\Amasty\Faq\Model\Question $question)
    {
        $urlKey = $this->configProvider->getUrlKey();
        return $this->_urlBuilder->getUrl($urlKey . '/' . $question->getCanonicalUrl());
    }
}
