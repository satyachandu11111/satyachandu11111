<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


namespace Amasty\Xnotif\Controller;

use Magento\Framework\App\RequestInterface;

abstract class AbstractIndex extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $customerSessionFactory;

    /**
     * @var \Magento\Customer\Model\Session|null
     */
    private $customerSession = null;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $redirectFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Magento\ProductAlert\Helper\Data
     */
    protected $productAlertHelper;

    /**
     * @var \Amasty\Xnotif\Helper\Config
     */
    protected $config;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\ProductAlert\Helper\Data $productAlertHelper,
        \Amasty\Xnotif\Helper\Config $config
    ) {
        parent::__construct($context);
        $this->customerSessionFactory = $customerSessionFactory;
        $this->redirectFactory = $context->getResultRedirectFactory();
        $this->resultPageFactory = $resultPageFactory;
        $this->productAlertHelper = $productAlertHelper;
        $this->config = $config;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->initLayout();
        $navigationBlock = $resultPage->getLayout()->getBlock(
            'customer_account_navigation'
        );

        if ($navigationBlock) {
            $navigationBlock->setActive('xnotif/' . static::TYPE . '/index');
        }
        $resultPage->getConfig()->getTitle()->prepend($this->getTitle());

        return $resultPage;
    }

    /**
     * Retrieve customer session object
     *
     * @return \Magento\Customer\Model\Session
     */
    private function getSession()
    {
        if ($this->customerSession === null) {
            $this->customerSession = $this->customerSessionFactory->create();
        }

        return $this->customerSession;
    }

    /**
     * Check customer authentication
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->getSession()->authenticate() || !$this->isActive()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
            $this->_redirect($this->_redirect->getRefererUrl());
        }

        return parent::dispatch($request);
    }

    /**
     * @return string
     */
    abstract public function getTitle();

    /**
     * @return bool
     */
    abstract protected function isActive();
}
