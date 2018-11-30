<?php
namespace Mirasvit\Feed\Controller\Adminhtml\Dynamic;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\Feed\Model\Dynamic\VariableFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;

abstract class Variable extends Action
{
    /**
     * @var VariableFactory
     */
    protected $variableFactory;

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
     * @param VariableFactory $variableFactory
     * @param Registry        $registry
     * @param Context         $context
     * @param ForwardFactory  $resultForwardFactory
     */
    public function __construct(
        VariableFactory $variableFactory,
        Registry $registry,
        Context $context,
        ForwardFactory $resultForwardFactory
    ) {
        $this->variableFactory = $variableFactory;
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
        $resultPage->getConfig()->getTitle()->prepend(__('Dynamic Variables'));

        return $resultPage;
    }

    /**
     * Current feed model
     * @return \Mirasvit\Feed\Model\Dynamic\Variable
     */
    protected function initModel()
    {
        $model = $this->variableFactory->create();

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
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Feed::feed_dynamic_variable');
    }
}
