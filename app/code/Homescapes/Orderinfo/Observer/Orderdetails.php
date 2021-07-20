<?php

namespace Homescapes\Orderinfo\Observer;

class Orderdetails implements \Magento\Framework\Event\ObserverInterface
{

	public function __construct(
		
		\Magento\Sales\Model\OrderRepository $orderRepository
	){
		$this->orderRepository = $orderRepository;
   }
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		//die('before place order..');
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/orderinfo.log');
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
		$this->_logger->info('------------ x START Order Details x------------');

			
		$order = $observer->getEvent()->getOrder();
		
		
		if($order instanceof \Magento\Framework\Model\AbstractModel){
			
			
			$createdDate = strtotime($order->getCreatedAt()); 
			$createdDate=date('Y/m/d', $createdDate); 
			$requestParams=array(
				
				"OrderId"=>$order->getData("increment_id"),
				"OrderStatus"=>$order->getStatus(),				
				"Customer Id"=>$order->getData("customer_id"),
				"Customer Email"=>$order->getData("customer_email"),
				"Customer Address"=>$order->getShippingAddress()->getData(),	
				"Customer Mobile"=>$order->getBillingAddress()->getTelephone(),	
				"Customer Name"=>$order->getBillingAddress()->getFirstName()." ".$order->getBillingAddress()->getLastName(),
				"subtotal"=>$order->getData("subtotal"),
				"Coupon Code"=>$order->getData("coupon_rule_name"),
				"Coupon Charges"=>$order->getData("base_discount_amount"),
				"Delivery Charges"=>$order->getData("base_shipping_amount"),
				"Discount"=>$order->getData("base_discount_amount"),			
				"Vat Percent"=>$order->getData("base_tax_amount"),			
				"Vat"=>$order->getData("base_tax_amount"),			
				"Grand Total"=>$order->getGrandTotal(),
				"Items"=>[],
			);

			//tax calculation
			if($order->getData("base_tax_amount")){
				$taxPer=($order->getData("base_tax_amount")*100)/$order->getData('subtotal');
				if($taxPer){
					$requestParams['VatPercent']=$taxPer;
				}
			}
			if($order->getData("base_discount_amount")){
				$DiscountPercent=($order->getData("base_discount_amount")*100)/$order->getData('subtotal');
				if($DiscountPercent){
					$requestParams['DiscountPercent']=$DiscountPercent;
				}
			}
			$payment = $order->getPayment();
		    // $method = $payment->getMethodInstance();
		    $methodTitle = $payment->getMethod();
		    $requestParams['Payment Method']=$methodTitle;

			foreach ($order->getAllItems() as $item)
			{
				//$this->_logger->info($order->getId().' = Order item data:'.json_encode($item->getData()));
				//if($item->getPrice() > 0){
					$requestParams['Items'][]=array(
					"itemId"=>$item->getProductId(),
					"sku"=>$item->getSku(),
					"qty"=>$item->getQtyOrdered(),
					"price"=>$item->getPrice(),
				);		 	
				//}
				
			}
			$this->_logger->info("Payment Method =".$methodTitle);
			
			$this->_logger->info($requestParams);
				
			$this->_logger->info('------------ x END x------------');
		}			
		
	}
   
}
