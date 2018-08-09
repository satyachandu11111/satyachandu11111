<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    private $actionFactory;

    /**
     * @var \Amasty\Faq\Model\ResourceModel\Category
     */
    private $category;

    /**
     * @var \Amasty\Faq\Model\ResourceModel\Question
     */
    private $question;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\RequestInterface|\Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Amasty\Faq\Model\ConfigProvider
     */
    private $configProvider;

    /**
     * Router constructor.
     *
     * @param \Magento\Framework\App\ActionFactory       $actionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Amasty\Faq\Model\ResourceModel\Category   $category
     * @param \Amasty\Faq\Model\ResourceModel\Question   $question
     * @param \Amasty\Faq\Model\ConfigProvider           $configProvider
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Faq\Model\ResourceModel\Category $category,
        \Amasty\Faq\Model\ResourceModel\Question $question,
        \Amasty\Faq\Model\ConfigProvider $configProvider
    ) {
        $this->actionFactory = $actionFactory;
        $this->category = $category;
        $this->question = $question;
        $this->storeManager = $storeManager;
        $this->configProvider = $configProvider;
    }

    /**
     * Match application action by request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $this->request = $request;

        $route = $this->configProvider->getUrlKey();
        if ($this->request->getFrontName() !== $route
            || $this->request->getModuleName()
            || !$this->configProvider->isEnabled()
        ) {
            return null;
        }

        $this->request->setModuleName('faq');

        $urlKey = $this->getUrlKey();
        if ($urlKey) {
            if (!$this->matchCategory($urlKey) && !$this->matchQuestion($urlKey) && !$this->matchStat($urlKey)) {
                return null;
            }
        } else {
            $this->request->setControllerName('index')->setActionName('index');
        }

        return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
    }

    /**
     * @return bool|string
     */
    private function getUrlKey()
    {
        $path = explode('/', urldecode(trim($this->request->getPathInfo(), '/')));
        if (!empty($path[1])) {
            return $path[1];
        }

        return false;
    }

    /**
     * @param string $urlKey
     *
     * @return bool
     */
    private function matchCategory($urlKey)
    {
        $categoryId = $this->category->checkUrlKey($urlKey, (int) $this->storeManager->getStore()->getId());
        if ($categoryId) {
            $this->request->setControllerName('category')
                ->setActionName('view')
                ->setParam('id', $categoryId);

            return true;
        }

        return false;
    }

    /**
     * @param string $urlKey
     *
     * @return bool
     */
    private function matchQuestion($urlKey)
    {
        $questionId = $this->question->checkUrlKey($urlKey, $this->storeManager->getStore()->getId());
        if ($questionId) {
            $this->request->setControllerName('question')
                ->setActionName('view')
                ->setParam('id', $questionId);

            return true;
        }

        return false;
    }

    private function matchStat($urlKey)
    {
        if ($urlKey == 'stat' && $this->request->isPost()) {
            $this->request->setControllerName('stat')
                ->setActionName('collect');

            return true;
        }

        return false;
    }
}
