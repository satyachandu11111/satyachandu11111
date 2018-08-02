<?php

/**
 * CollectPlus
 *
 * @category    CollectPlus
 * @package     Jjcommerce_CollectPlus
 * @version     2.0.0
 * @author      Jjcommerce Team
 *
 */

namespace Jjcommerce\CollectPlus\Controller\Index;


class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     *
     */
    protected $_jsonHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Psr\Log\LoggerInterface $logger //log injection

    ) {
        $this->_jsonHelper = $jsonHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        parent::__construct(
            $context
        );
    }

    public function execute()
    {

        $block = $this->_view->getLayout()->createBlock('Jjcommerce\CollectPlus\Block\Collect');
        $block->setTemplate('collect.phtml');
        return $this->getResponse()->setBody($this->_jsonHelper->jsonEncode(array('html' => $block->toHtml())));

    }
}
