<?php
namespace Homescapes\Orderswatch\Controller\Ajax;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class ProductDetail extends \Magento\Framework\App\Action\Action
{
	protected $coreSession;
        
        protected $jsonHelper;
        
        protected $resultFactory;

        protected $_resultPageFactory;
        
        protected $helper;
        
        protected $url;

        public function __construct(
                Context $context,
                \Magento\Framework\Session\SessionManagerInterface $coreSession,                                
                \Homescapes\Orderswatch\Helper\Data  $helper,
                array $data = [])
        {
            parent::__construct($context);
            $this->coreSession = $coreSession;                        
            $this->helper = $helper;
            $this->url = $context->getUrl();
        }
        
     

        public function execute()
	{
            
            
            $swatchsession = $this->coreSession->getHomescapessampleswatch();
            $productId  =  $this->getRequest()->getParam('id');    
            
            $_product = $this->helper->getLoadProduct($productId);            
            
            $newcls='';
            $customSwatchClass = '';
            $sampletext = __('Order Free Sample Swatch');
            if($_product->getSampleSwatchText())
            {
                $customSwatch = explode('-',$_product->getSampleSwatchText());
                $sampletext = $customSwatch[0];
                if(sizeof($customSwatch) == 2){
                    $customSwatchClass = $customSwatch[1];
                }
            }
            $buttonevent=0;
            if($swatchsession!='')
            {
                $sampleswatchsession=explode(',',$swatchsession); 

            }
           if(isset($sampleswatchsession) && count($sampleswatchsession)>0)
           {
            if (in_array($_product->getEntityId(),$sampleswatchsession))
            { 
                $sampletext= __('Remove swatch'); $buttonevent=$_product->getEntityId();
                $newcls='added';

            }
          }
            
            
            $productSku = $_product->getSku();
            $productName = $_product->getName();
            $smallImage =  $this->helper->getsmallImg($_product);
            $url = $this->url->getUrl('orderswatches/ajax/index');
            
            $html = '<input type="hidden" value="'.$buttonevent.'" id="swatchevent" />';
            $html .= '<strong class="order-swatch '.$customSwatchClass.' '.$newcls.'" data-url="'.$url.'" data-name="'.$productName.'" data-img="'.$smallImage.'" data-id="'.$_product->getId().'"  title="'.$sampletext.'" type="button"><span class="view-swatch-label-'.$_product->getId().'">'.$sampletext.'</span></strong>';
            $html .= '<p style="display:none" class="swatch-quantity-error" role="alert"><span class="swatch-quantity-error-message">You can only order up to 5 swatches</span></p>';
            $arrayjson=array('html'=>$html);
        
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($arrayjson);
            return $resultJson; 
            
		
	}
}
