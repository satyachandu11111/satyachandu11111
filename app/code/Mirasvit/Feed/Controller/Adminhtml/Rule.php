<?php

namespace Mirasvit\Feed\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Mirasvit\Feed\Model\RuleFactory;

abstract class Rule extends Action
{
    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var Registry
     */
    protected $registry;


    /**
     * @var Context
     */
    protected $context;

    /**
     * {@inheritdoc}
     * @param RuleFactory    $ruleFactory
     * @param Registry       $registry
     * @param Context        $context
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        RuleFactory $ruleFactory,
        Registry $registry,
        Context $context,
        ForwardFactory $resultForwardFactory
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->resultForwardFactory = $resultForwardFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     * @param \Magento\Backend\Model\View\Result\Page\Interceptor $resultPage
     * @return \Magento\Backend\Model\View\Result\Page\Interceptor
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Magento_Catalog::catalog');
        $resultPage->getConfig()->getTitle()->prepend(__('Advanced Product Feeds'));
        $resultPage->getConfig()->getTitle()->prepend(__('Filters'));

        return $resultPage;
    }

    /**
     * Current template model
     *
     * @return \Mirasvit\Feed\Model\Rule
     */
    public function initModel()
    {
        $model = $this->ruleFactory->create();

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        if ($this->getRequest()->getParam('type')) {
            $model->setType($this->getRequest()->getParam('type'));
        }

        if ($this->getRequest()->getParam('feed')) {
            $model->setFeedIds([$this->getRequest()->getParam('feed')]);
        }

        $this->registry->register('current_model', $model);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Feed::feed');
    }
}
