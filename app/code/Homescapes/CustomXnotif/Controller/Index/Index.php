<?php

namespace Homescapes\CustomXnotif\Controller\Index;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Action\Action;
use Magento\ProductAlert\Block\Product\View;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
 
class Index extends \Magento\Framework\App\Action\Action 
{

    protected $_pageFactory;
    protected $_productRepository;
    protected $_storeManager;
    protected $jsonHelper;
    protected $xnotifHelper;
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Catalog\Model\ProductRepository $productRepository, 
            \Magento\Store\Model\StoreManagerInterface $storeManager, 
            \Magento\Framework\View\Result\PageFactory $pageFactory,
            \Magento\Framework\Json\Helper\Data $jsonHelper
        ) 
    {
        $this->_pageFactory = $pageFactory;
        $this->_productRepository = $productRepository;
        $this->_storeManager = $storeManager;
        $this->jsonHelper = $jsonHelper;
        //$this->registry = $registry;
        
        return parent::__construct($context);
    }
     public function execute() {
        $post = $this->getRequest()->getPost();
        $product_id=$post['productid'];
        $parentproductid=$post['parentproduct'];
        //$resultPage->getConfig()->getTitle()->prepend(__(' heading '));

        

        if($product_id!=''){
            $product = $this->_productRepository->getById($product_id);
        
        $html='';
        $status='';
        if($product->isSaleable()){
            $html='';
            $status='in-stock';
        }else{
            

            /*$objectManager = \Magento\Core\Model\ObjectManager::getInstance();
            $helperFactory = $objectManager->get('\Magento\Core\Model\Factory\Helper');
            $helper = $helperFactory->get('\Amasty\CustomXnotif\Helper\Data');
            //$html=$helper->getStockAlert($product);*/
            $alertBlock =  $this->createDefaultAlertBlock();
            //echo "store id :".  $this->_storeManager->getStore()->getStoreId();
            $html= $this->observeStockAlertBlock($product, $alertBlock, $parentproductid);
            $status='out-of-stock';
        }
        }else{
        $html='No data';
        $status='in-stock';    
        }
        
        /*$html = $resultPage->getLayout()
                ->createBlock('\Magento\ProductAlert\Block\Product\View')
                ->setTemplate('Magento_ProductAlert::product/view.phtml')
                ->toHtml();*/
       
        $result = array(
              'popup' => $html,
              'status' => $status,
              'message' => 'success',
        );

        echo $this->jsonHelper->jsonEncode($result); exit;
    }
    /**
     * @param ProductInterface $product
     * @param View $alertBlock
     *
     * @return string
     */
    public function observeStockAlertBlock(ProductInterface $product, View $alertBlock,$parentproduct)
    {
        $html = '';
        if($parentproduct){
        	
        	$parentProductModel = $this->_productRepository->getById($parentproduct);
        }
    	
        $alertBlock->setSignupUrl($this->getSignupUrl(
            'stock',
            $product->getId(),
            $parentproduct
        ));
        $alertBlock->setOriginalProduct($product);
        $alertBlock->setParentProductId($parentproduct);
        $productUrl=$parentProductModel->getProductUrl();
        $alertBlock->setBackUrl($productUrl);

        if ($alertBlock && !$product->getData('amxnotif_hide_alert')) {
            /*if (!$this->isLoggedIn()) {*/
                $alertBlock->setTemplate('Amasty_Xnotif::product/view_email_configurable.phtml');
            //}

            $alertBlock->setData('amxnotif_observer_triggered', 1);
            $html = $alertBlock->toHtml();
            $alertBlock->setData('amxnotif_observer_triggered', null);
        }

        return $html;
    }
 
   /**
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    protected function createDefaultAlertBlock()
    {
        
        $resultPage = $this->_pageFactory->create();
        $alertBlock = $resultPage->getLayout()
                ->createBlock('\Magento\ProductAlert\Block\Product\View')
                ->setTemplate('Magento_ProductAlert::product/view.phtml');

        //$alertBlock->setTemplate('Magento_ProductAlert::product/view.phtml');
        $alertBlock->setHtmlClass('alert stock link-stock-alert');
        $alertBlock->setSignupLabel(__('Sign up to get notified when this configuration is back in stock'));
        
        return $alertBlock;
    }

    /**
     * @param $type
     * @param int $productId
     * @param null|int $parentId
     * @param bool $addUencInUrl
     *
     * @return string
     */
    public function getSignupUrl($type, $productId, $parentId = null, $addUencInUrl = true)
    {
    	$url=
        $params = ['product_id' => $productId];
        

        if ($parentId) {
            $params['parent_id'] = $parentId;
        }

        return $this->_url->getUrl('xnotif/email/' . $type, $params);// 
    }
}