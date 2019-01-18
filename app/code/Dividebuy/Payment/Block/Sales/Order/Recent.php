<?php

namespace Dividebuy\Payment\Block\Sales\Order;

/**
 * Sales order history block
 */
class Recent extends \Magento\Sales\Block\Order\Recent
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context           $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\Session                            $customerSession
     * @param \Magento\Sales\Model\Order\Config                          $orderConfig
     * @param array                                                      $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        array $data = []
    ) {
        parent::__construct($context, $orderCollectionFactory, $customerSession, $orderConfig, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $orders = $this->_orderCollectionFactory->create()->addAttributeToSelect(
            '*'
        )->addFieldToFilter(
            'hide_dividebuy',
            array('eq' => 0)
        )->addAttributeToFilter(
            'customer_id',
            $this->_customerSession->getCustomerId()
        )->addAttributeToFilter(
            'status',
            ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
        )->addAttributeToSort(
            'created_at',
            'desc'
        )->setPageSize(
            '5'
        )->load();
        $this->setOrders($orders);
    }

    /**
     * Used to get HTML
     * 
     * @return html
     */
    protected function _toHtml()
    {
        $this->setModuleName($this->extractModuleName('Magento\Sales\Block\Order\Recent'));
        return parent::_toHtml();
    }

}
