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


class Smsalert extends \Magento\Framework\App\Action\Action
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
        \Magento\Quote\Model\Quote $quote,
        \Psr\Log\LoggerInterface $logger, //log injection
        \Jjcommerce\CollectPlus\Model\Checkout\Type\Onepage $agentQuote
    ) {
        $this->_jsonHelper = $jsonHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->quote = $quote;
        $this->agentquote = $agentQuote;
        $this->quoteRepository = $quoteRepository;
        parent::__construct(
            $context
        );
    }

    public function execute()
    {
        try {
            $data = $this->getRequest()->getParams();
            $this->agentquote->saveSmsNumber($data);
            $this->getResponse()->setBody($this->_jsonHelper->jsonEncode(array('success' => 'OK')));
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
            $this->getResponse()->setBody($this->_jsonHelper->jsonEncode(array('success' => 'OK')));
        }

    }
}
