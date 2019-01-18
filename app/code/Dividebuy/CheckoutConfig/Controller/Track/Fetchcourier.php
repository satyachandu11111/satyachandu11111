<?php
namespace Dividebuy\CheckoutConfig\Controller\Track;

use Dividebuy\CheckoutConfig\Block\Cart as CheckoutBlock;
use Dividebuy\CheckoutConfig\Helper\Data as CheckoutHelper;
use Dividebuy\Payment\Helper\Data as PaymentHelper;
use Magento\Framework\App\Action\Context;

class Fetchcourier extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var CheckoutBlock
     */
    protected $_checkoutBlock;

    /**
     * @var CheckoutHelper
     */
    protected $_checkoutHelper;

    /**
     * @var PaymentHelper
     */
    protected $_paymentHelper;
    /**
     * @var \Magento\Store\Model\ScopeInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_divideBuylogger;

    /**
     * @param Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param CheckoutHelper                             $checkoutHelper
     * @param CheckoutBlock                              $checkoutBlock
     * @param PaymentHelper                              $paymentHelper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        CheckoutHelper $checkoutHelper,
        CheckoutBlock $checkoutBlock,
        PaymentHelper $paymentHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Dividebuy\RetailerConfig\Logger\Logger $divideBuylogger
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_checkoutBlock     = $checkoutBlock;
        $this->_checkoutHelper    = $checkoutHelper;
        $this->_paymentHelper     = $paymentHelper;
        $this->_scopeConfig          = $scopeConfig;
        $this->_divideBuylogger         = $divideBuylogger;

        parent::__construct($context);
    }

    /**
     * Retrieves the couriers detail
     */
    public function execute()
    {
        $storeId  = $this->_checkoutHelper->getStoreId();
        $couriers = unserialize($this->_checkoutHelper->getCouriers($storeId));
        if ($couriers) {
            $result = array("status" => "ok", "couriers" => $couriers);
        } else {
            $result = array("error" => 1, "message" => "DivideBuy Courrier list not found");
        }
        /*For error log code start*/
        $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,empty($storeId) ? 0 : $storeId);
        if($errorLogStatus == 1)
        {
            $dataResponse = "============\n";
            $dataResponse .= "Url : fetch couriers \n";
            $dataResponse .= "Response :".json_encode($result)."\n";
            $this->_divideBuylogger->info($dataResponse);    
        } 
        /*Error log code end*/
        $this->_paymentHelper->_prepareDataJSON($result);
    }
}
