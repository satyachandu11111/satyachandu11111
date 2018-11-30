<?php

namespace Mirasvit\Feed\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Mirasvit\Feed\Model\TemplateFactory;

abstract class Template extends Action
{
    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * {@inheritdoc}
     * @param TemplateFactory $pageFactory
     * @param Registry        $registry
     * @param Context         $context
     * @param ForwardFactory  $resultForwardFactory
     */
    public function __construct(
        TemplateFactory $pageFactory,
        Registry $registry,
        Context $context,
        ForwardFactory $resultForwardFactory
    ) {
        $this->templateFactory = $pageFactory;
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
        $resultPage->getConfig()->getTitle()->prepend(__('Templates'));

        return $resultPage;
    }

    /**
     * Current template model
     *
     * @return \Mirasvit\Feed\Model\Template
     */
    public function initModel()
    {
        $model = $this->templateFactory->create();

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        $this->registry->register('current_model', $model);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Feed::feed_template');
    }
}
