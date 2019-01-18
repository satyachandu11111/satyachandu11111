<?php
namespace Dividebuy\CheckoutConfig\Block\Adminhtml\Order\Tracking;

/**
 * Shipment tracking control form
 *
 */
class View extends \Magento\Shipping\Block\Adminhtml\Order\Tracking\View
{
    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Shipping\Model\Config          $shippingConfig
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Shipping\Model\CarrierFactory  $carrierFactory
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        array $data = []
    ) {
        parent::__construct($context, $shippingConfig, $registry, $carrierFactory, $data);
        $this->_carrierFactory = $carrierFactory;

    }

    /**
     * Used to get carrier title
     * 
     * @param string $code
     * @return \Magento\Framework\Phrase|string|bool
     */
    public function getCarrierTitle($code)
    {
        $carrier = $this->_carrierFactory->create($code);
        if ($carrier) {
            return $carrier->getConfigData('title');
        } elseif ($code == "dividebuy_custom") {
            return __('Dividebuy Custom');
        } else {
            return __('Custom Value');
        }
        return false;
    }

    /**
     * Retrieve carriers
     * 
     * @return array
     */
    public function getCarriers()
    {
        $carriers                     = [];
        $carrierInstances             = $this->_getCarriersInstances();
        $carriers['custom']           = __('Custom Value');
        $carriers['dividebuy_custom'] = 'DivideBuy Custom';
        foreach ($carrierInstances as $code => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                $carriers[$code] = $carrier->getConfigData('title');
            }
        }
        return $carriers;
    }

    /**
     * Returns HTML
     * 
     * @return html
     */
    protected function _toHtml()
    {
        $this->setModuleName($this->extractModuleName('Magento\Shipping\Block\Adminhtml\Order\Tracking\View'));
        return parent::_toHtml();
    }
}