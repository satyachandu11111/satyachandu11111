<?php

namespace Homescapes\Ordermanagers\Controller\Adminhtml\Index;

use Magento\Framework\App\Helper\Context;

class Index extends \Magento\Backend\App\Action
{

    /**
     * scope config 
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_downloader;

    protected $_orderCollectionFactory;


	public function __construct(
	    \Magento\Framework\App\Action\Context $context,
	    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
	    \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
	) { 
        $this->_downloader = $fileFactory;
		$this->_orderCollectionFactory = $orderCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->_scopeConfig = $scopeConfig;
        $this->_countryFactory = $countryFactory;
        $this->_priceHelper = $priceHelper;
        $this->_productloader = $_productloader;
        $this->_configurableProduct = $configurableProduct;
        $this->attributeSet = $attributeSet;
        $this->_messageManager = $messageManager;
        $this->_categoryFactory = $categoryFactory;
        $this->_storeManager = $storeManager;
        $this->_dir = $dir;
        parent::__construct($context);
	}

    public function execute()
    {   
        $this->getOrderCollection();
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }


    function getOrderCollection()
    {
        $orderlimit = $this->_scopeConfig->getValue('order_bulkexport/export_orders/orderlimit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $fromDate = $this->_scopeConfig->getValue('order_bulkexport/export_orders/fromdate', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $toDate = $this->_scopeConfig->getValue('order_bulkexport/export_orders/todate', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $storeId = $this->_scopeConfig->getValue('order_bulkexport/export_orders/dropdown', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        // echo '<pre>';print_r($orderData);die;       



        $fromDate = date('Y-m-d G:i:s', strtotime(str_replace('/', '-', $fromDate)));
        $toDate = date('Y-m-d G:i:s', strtotime(str_replace('/', '-', $toDate)));      

        $arrayToCsv[] = array('Order #','Purchased From','Purchased On','Product Name','SKU','Attribute Set','Linnworks Category','Category','Qty','Bill to Name','Ship to Name','Bill to Company','Ship to Company','Bill to Street','Ship to Street','Bill to City','Ship to City','Bill to State','Ship to State','Bill to Country','Ship to Country','Billing Postcode','Shipping Postcode','Billing Telephone','Shipping Telephone','Shipping Method','Shipped','Customer Email','Payment Method','Coupon Code','Discount Amount','G.T. (Base)','G.T. (Purchased)','order Status','Shipping Amount','Subtotal','Row Total');


        // echo "<pre>";
        $now = new \DateTime();
        $orderCollection = $this->_orderCollectionFactory->create()->addAttributeToSelect('*')
        ->addFieldToFilter('created_at', ['gteq' => $now->format($fromDate)])
        ->addFieldToFilter('created_at', ['lteq' => $now->format($toDate)])
        ->addFieldToFilter('store_id', $storeId);
        $orderCollection->getSelect()->limit(trim($orderlimit));

        $storeName = $this->getStoreName($storeId);

		foreach ($orderCollection as $order) {
                $orderItems = $this->orderRepository->get($order->getEntityId());
                foreach ($orderItems->getAllItems() as $item) {
                    $skip = 0;

                    $shipmentCollection = $order->getShipmentsCollection();
                    $shipmentId = [];
                    foreach ($shipmentCollection as $shipment) {
                        $shipmentId = $shipment->getId();
                    }
                    if ($shipmentId !== "") {
                        $shipped = "Yes";
                    }else{
                        $shipped = "No";
                    }
                    

                    // $shpping = $order->getShipmentsCollection();
                    // print_r($shpping->getData());

                    $payment = $order->getPayment();
                    $method = $payment->getMethodInstance();
                    


                    $rowTotal = $item->getRowTotal() + $item->getTaxAmount() + $item->getHiddenTaxAmount() - $item->getDiscountAmount();


                    $shippingAddress = $order->getShippingAddress();
                    $billingAddress = $order->getBillingAddress();
                    
                    $billingCountry = $this->_countryFactory->create()->loadByCode($billingAddress->getData('country_id'));
                    $shippingCountry = $this->_countryFactory->create()->loadByCode($shippingAddress->getData('country_id'));
                    
                    
                    
                    $productData = $this->productData($item->getProductId());
                    $cats = $productData->getCategoryIds();
                    $categoryCount = count($cats);
                    if($categoryCount){
                        $count = 1;
                            $categoryName = "";
                        foreach ($cats as $categoryId) {
                            $_category = $this->_categoryFactory->create()->load($categoryId);
                            $categoryName .= $_category->getName();
                            if($count < $categoryCount) {
                                $categoryName .= "|";
                            }
                            $count++;
                        }
                    }

                    $attributeSetRepository = $this->attributeSet->get($productData->getAttributeSetId());
                    
                    // print_r($shippingaddress);

                    if ($productData->getTypeId() == 'simple') {
                        $parentId = $this->_configurableProduct->getParentIdsByChild($item->getProductId());
                        if(!empty($parentId)){
                            $skip = 1;
                        }
                    }

                    if ($skip !== 1) {
                        $arrayToCsv[] 
                            = array(
                            $order->getIncrementId(),
                            $storeName,
                            date("d M Y G:i:s", strtotime($order->getCreatedAt())),

                            $item->getName(),
                            $item->getSku(),
                            $attributeSetRepository->getAttributeSetName(),
                            $productData->getLinnworksCategory(),
                            $categoryName,
                            $item->getQtyOrdered(),

                            $billingAddress->getData('firstname')." ".$billingAddress->getData('middlename')." ".$billingAddress->getData('lastname'),
                            $shippingAddress->getData('firstname')." ".$shippingAddress->getData('middlename')." ".$shippingAddress->getData('lastname'),

                            $billingAddress->getData('company'),
                            $shippingAddress->getData('company'),

                            $billingAddress->getData('street'),
                            $shippingAddress->getData('street'),

                            $billingAddress->getData('city'),
                            $shippingAddress->getData('city'),

                            $billingAddress->getData('region'),
                            $shippingAddress->getData('region'),

                            $billingCountry->getName(),
                            $shippingCountry->getName(),

                            $billingAddress->getData('postcode'),
                            $shippingAddress->getData('postcode'),

                            $billingAddress->getData('telephone'),
                            $shippingAddress->getData('telephone'),

                            $order->getShippingMethod(),

                            $shipped,

                            $order->getCustomerEmail(),

                            $payment->getMethod(),

                            $order->getCouponCode(),

                            $this->_priceHelper->currency($order->getDiscountAmount(), true, false),

                            $this->_priceHelper->currency($item->getOriginalPrice(), true, false),
                            $this->_priceHelper->currency($item->getBaseOriginalPrice(), true, false),

                            $order->getStatus(),

                            $this->_priceHelper->currency($order->getShippingAmount(), true, false),

                            $this->_priceHelper->currency($order->getSubtotal(), true, false),

                            $this->_priceHelper->currency($rowTotal, true, false)
                        );
                    }
                }
		}


        // print_r($arrayToCsv);
        // die('-*-*-*-');
        $finalFileName = "order_export_".date("Ymd_His").'.csv';
        $resultData = $this->generateCsv($arrayToCsv, $finalFileName);       
        



        if (empty($resultData)) {
            $this->_messageManager->addErrorMessage('Somthing Went Wrong!');
        } else {
            $this->_messageManager->addSuccessMessage($finalFileName.' File has been downloaded');
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.$finalFileName.'"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($resultData)); //Absolute URL
            ob_clean();
            flush();
            readfile($resultData); //Absolute URL
            exit();
        }
    }

    function generateCsv($data, $finalFileName, $delimiter = ',', $enclosure = '"') {
        $rootPath = $this->_dir->getRoot()."/";
        $csvFileName = "var/importexport/custom_order_csv/".$finalFileName;
        $finaCsvPath = $rootPath.$csvFileName;
        $handle = fopen($finaCsvPath, 'w');
        foreach ($data as $line) {
            fputcsv($handle, $line, $delimiter, $enclosure);
        }
        fclose($handle);
        if(file_exists($csvFileName)){
            return $csvFileName;
        }else{
            return false;
        }
    }

    public function productData ($productId)
    {
        return $this->_productloader->create()->load($productId);
    }

    public function getStoreName($storeId){

        $storeManager = $this->_storeManager;
        $storeId = $storeId;
            $stores = $storeManager->getStores(true, false);
            foreach($stores as $store){
            if($store->getId() === $storeId){
                $storeName = $store->getName();
            }
           }
        return $storeName;
    }

}

