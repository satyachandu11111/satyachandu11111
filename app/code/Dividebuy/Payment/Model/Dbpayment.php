<?php
namespace Dividebuy\Payment\Model;

use Magento\Framework\DataObject;

/**
 * Cash on delivery payment method model
 */
class Dbpayment extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_METHOD_DIVIDEBUY_CODE = 'dbpayment';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_DIVIDEBUY_CODE;

    /**
     * Cash On Delivery payment block paths
     *
     * @var string
     */
    protected $_formBlockType = 'Dividebuy\Payment\Block\Form\Dbpayment';

    /**
     * Info instructions block path
     *
     * @var string
     */
    protected $_infoBlockType = 'Magento\Payment\Block\Info\Instructions';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;

    public function getOrderPlaceRedirectUrl()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $urlBuilder    = $objectManager->get('Magento\Framework\UrlInterface');
        return $urlBuilder->getUrl("dividebuy/payment/redirect");
    }

    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }

    /**
     * Check whether payment method can be used
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        // Checking if quote is null.
        if (empty($quote)) {
            return false;
        }

        $objectManager        = \Magento\Framework\App\ObjectManager::getInstance();
        $retailerConfigHelper = $objectManager->get('\Dividebuy\RetailerConfig\Helper\Data');
        $checkoutConfigHelper = $objectManager->get('\Dividebuy\CheckoutConfig\Helper\Data');
        $isIPAllowed          = $retailerConfigHelper->isIPAllowed($quote->getStoreId());
        $extensionStatus      = $retailerConfigHelper->getExtensionStatus($quote->getStoreId());
        $dividebuyItems = $checkoutConfigHelper->getDividebuyItemArray($quote->getId());

        if (!$this->isActive($quote ? $quote->getStoreId() : null) || !$isIPAllowed || !$extensionStatus || empty($dividebuyItems['dividebuy'])) {
            return false;
        }

        // Appending DivideBuy payment method to checkout.
        $checkResult = new DataObject();
        $checkResult->setData('is_available', true);

        // for future use in observers
        $this->_eventManager->dispatch(
            'payment_method_is_active',
            [
                'result'          => $checkResult,
                'method_instance' => $this,
                'quote'           => $quote,
            ]
        );

        return $checkResult->getData('is_available');        
    }
}
