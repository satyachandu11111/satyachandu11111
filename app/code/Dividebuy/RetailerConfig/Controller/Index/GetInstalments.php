<?php
namespace Dividebuy\RetailerConfig\Controller\Index;

use Dividebuy\CheckoutConfig\Block\Cart as CheckoutBlock;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class GetInstalments extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @var \Dividebuy\RetailerConfig\Helper\Data
     */
    protected $_retailerConfigHelper;

    /**
     * @var CheckoutBlock
     */
    protected $_checkoutBlock;

    /**
     * @param \Magento\Framework\App\Action\Context      $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\CurrencyFactory   $currencyFactory
     * @param \Dividebuy\RetailerConfig\Helper\Data      $retailerConfigHelper
     * @param CheckoutBlock                              $checkoutBlock
     */
    public function __construct(\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Dividebuy\RetailerConfig\Helper\Data $retailerConfigHelper,
        \Magento\Directory\Model\Currency $currency,
        CheckoutBlock $checkoutBlock
    ) {
        parent::__construct($context);
        $this->resultPageFactory     = $resultPageFactory;
        $this->_storeManager         = $storeManager;
        $this->_currencyFactory      = $currencyFactory;
        $this->_retailerConfigHelper = $retailerConfigHelper;
        $this->currency              = $currency;
        $this->_checkoutBlock        = $checkoutBlock;
    }

    /**
     * Used get instalment details and load instalmentsDetails.phtml file
     *
     * @return html
     */
    public function execute()
    {
        $priceWithCurrency = $this->getRequest()->getparam("price");
        // $currencyCode      = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        $storeId           = $this->_storeManager->getStore()->getStoreId();

        // $currency       = $this->_currencyFactory->create()->load($currencyCode);
        $currencySymbol = $this->currency->getCurrencySymbol();

        $actualProductPrice = ltrim(str_replace(",", "", $priceWithCurrency), $currencySymbol);

        $instalmentDetails = $this->_retailerConfigHelper->getInstalmentDetails($actualProductPrice, $storeId);

        $modalBlock = $this->_checkoutBlock
            ->getLayout()
            ->createBlock('Dividebuy\CheckoutConfig\Block\Cart')
            ->assign(['instalment' => array($instalmentDetails, $priceWithCurrency)])
            ->setTemplate('Dividebuy_CheckoutConfig::dividebuy/product/instalments/instalmentsDetails.phtml')
            ->toHtml();

        $this->getResponse()
            ->setHeader('Content-Type', 'text/html')
            ->setBody($modalBlock);
        return;
    }
}
