<?php


namespace Mageplaza\Webhook\Factory;


use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Mageplaza\Webhook\Adapter\Order as SpbOrder;
use Mageplaza\Webhook\Adapter\Adjustment;

class OrderFactory
{

    /**
     * @var AddressFactory
     */
    private $addressFactory;

    /**
     * @var OrderItemFactory
     */
    private $orderItemFactory;

    /**
     * @var CreditMemoFactory
     */
    private $creditMemoFactory;

    /**
     * @var shipmentFactory
     */
    private $shipmentFactory;

    /**
     * @var adjustmentFactory
     */
    private $adjustmentFactory;


    public function __construct(AddressFactory $addressFactory, OrderItemFactory $orderItemFactory, CreditMemoFactory $creditMemoFactory, ShipmentFactory $shipmentFactory, AdjustmentFactory $adjustmentFactory)
    {
        $this->addressFactory = $addressFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->creditMemoFactory = $creditMemoFactory;
        $this->shipmentFactory = $shipmentFactory;
        $this->adjustmentFactory = $adjustmentFactory;
    }


    /**
     * @param Order $order
     */
    public function createFromOrder(OrderInterface $order): SpbOrder
    {
        $spbOrder = new SpbOrder();
        $spbOrder->setState($order->getStatus());
        $spbOrder->setOrderStatus($order->getState());
        $spbOrder->setCreatedAt($order->getCreatedAt());
        $spbOrder->setUpdatedAt($order->getUpdatedAt());
        $spbOrder->setCheckoutCompletedAt($order->getCreatedAt());
        //$spbOrder->setChannelOrderId($order->getId());
        $spbOrder->setChannelOrderId($order->getIncrementId());
        $spbOrder->setNumber($order->getId());
        $spbOrder->setCurrencyCode($order->getBaseCurrencyCode());
        $spbOrder->setNotes($order->getCustomerNote());
        $spbOrder->setCustomerEmail($order->getCustomerEmail());
//        $spbOrder->setOrderTotalAmount($order->getGrandTotal()*100);
//        $spbOrder->setOrderTotalAmount($order->getSubtotal()*100);
//        $spbOrder->setShippingAmount($order->getShippingAmount()*100);
//        $spbOrder->setState($order->getShippingAmount());
        $spbOrder->setBillingAddress($this->addressFactory->createFromAddress($order->getBillingAddress()));
        $spbOrder->setShippingAddress($this->addressFactory->createFromAddress($order->getShippingAddress()));
        $items = [];
        $creditMemos = [];
        $shipments = [];
        $adjustmentAmount = [];

        foreach ($order->getCreditmemosCollection() as $creditMemoRow){
            $creditMemos []= $this->creditMemoFactory->createFromCreditMemo($creditMemoRow);
        }

        foreach ($order->getItems() as $orderItem){
            if(count($orderItem->getChildrenItems()) > 0) continue;
            $items []= $this->orderItemFactory->createFromOrderItem($orderItem);
        }

        foreach ($order->getShipmentsCollection() as $shipmentRow){
            $shipments []= $this->shipmentFactory->createFromShipment($shipmentRow);
        }

        if ($order->getShippingAmount() && ($order->getShippingAmount() > 0)) {
            $type = Adjustment::SHIPPING;
            $adjustmentAmount[] = $this->adjustmentFactory->createFromAdjustment($order, $type,true, $order->getShippingAmount());
        }

        if ($order->getDiscountAmount() && ($order->getDiscountAmount() > 0)) {
            $type = Adjustment::DISCOUNT;
            $adjustmentAmount[] = $this->adjustmentFactory->createFromAdjustment($order, $type, false, $order->getDiscountAmount());
        }

        if ($order->getTaxAmount() && ($order->getTaxAmount() > 0)) {
            $type = Adjustment::TAX;
            $adjustmentAmount[] = $this->adjustmentFactory->createFromAdjustment($order, $type, true, $order->getTaxAmount());
        }

        if ($order->getDiscountRefunded() && ($order->getDiscountRefunded() > 0)) {
            $type = Adjustment::DISCOUNT_REFUNDED;
            $adjustmentAmount[] = $this->adjustmentFactory->createFromAdjustment($order, $type, false, $order->getDiscountRefunded());
        }

        if ($order->getShippingRefunded() && ($order->getShippingRefunded() > 0)) {
            $type = Adjustment::SHIPPING_REFUNDED;
            $adjustmentAmount[] = $this->adjustmentFactory->createFromAdjustment($order, $type, false, $order->getShippingRefunded());
        }

        if ($order->getTaxRefunded() && ($order->getTaxRefunded() > 0)) {
            $type = Adjustment::TAX_REFUNDED;
            $adjustmentAmount[] = $this->adjustmentFactory->createFromAdjustment($order, $type, false, $order->getTaxRefunded());
        }

        $spbOrder->setItems($items);
        $spbOrder->setCreditMemos($creditMemos);
        $spbOrder->setShipments($shipments);
        $spbOrder->setAdjustments($adjustmentAmount);
        return $spbOrder;
    }

}
