<?php
namespace Dividebuy\Payment\Helper;

class Order extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_orderModel;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_productModel;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_stockItemRepository;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $_quoteRepository;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $_cartManagement;

    /**
     * @var \Magento\Quote\Model\QuoteManagement
     */
    protected $quoteManagement;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Quote\Model\QuoteFactory $quoteRepository,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Quote\Api\CartManagementInterface $cartManagement
    ) {
        $this->_productModel        = $productModel;
        $this->_stockItemRepository = $stockItemRepository;
        $this->_cartManagement      = $cartManagement;
        $this->_quoteRepository     = $quoteRepository;
        $this->_orderModel          = $orderModel;
        $this->quoteManagement      = $quoteManagement;
        parent::__construct($context);
    }

    /**
     * Get product current available stock
     *
     * @param object $order
     * @return string.
     */
    public function getProductCurrentStock($order)
    {
        $stockAvailable = "true";
        $orderItems     = $order->getAllItems();

        foreach ($orderItems as $item) {
            if ((!$item->getParentItemId() && $item->getProductType() != "simple") && $item->getProductType() != "grouped") {
                continue;
            }

            $productQuantity = $item->getQtyOrdered();
            $product         = $this->_productModel->load($item->getProductId());
            $productType     = $product->getTypeId();
            $stock           = $this->_stockItemRepository->get($product->getId());
            // Checking if current product is simple and ordered quantity is greater than current stock.
            if (($productType == "simple" && round($stock->getQty()) < $productQuantity) || !$stock->getIsInStock()) {
                $stockAvailable = "false";
                break;
            }
        }
        return $stockAvailable;
    }

    /**
     * Generates new order based on current quote.
     *
     * @param integer $quoteId
     * @return integer
     */
    public function createNewOrder($quoteId)
    {
        $quote           = $this->_quoteRepository->create()->load($quoteId);
        $shipping_method = $quote->getShippingAddress()->getShippingMethod();
        $shippingAddress = $quote->getShippingAddress();
        if ($shipping_method == "freeshipping_freeshipping") {
            $shippingAddress->setFreeShipping(true);
        }
        $paymentMethod = $quote->getPayment()->getMethodInstance()->getCode();

        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod($shipping_method)
            ->setPaymentMethod($paymentMethod);

        // Set Sales Order Payment
        $quote->getPayment()->importData(array('method' => $paymentMethod));
        // Collect Totals & Save Quote
        $quote->setReservedOrderId('');
        $quote->collectTotals()->save();

        $orderPlaced = $this->quoteManagement->submit($quote);
        $orderPlaced->setEmailSent(0);
        $increment_id = $orderPlaced->getRealOrderId();

        if ($increment_id) {
            $order   = $this->_orderModel->load($increment_id, 'increment_id');
            $orderId = $order->getId();
            $quote->setIsActive(0)->save();
        }
        // Resource Clean-Up
        $quote = $orderPlaced = null;

        return $orderId;
    }
}
