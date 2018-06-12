<?php
namespace Homescapes\Orderswatch\Controller\Ajax;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $coreSession;
        
        protected $jsonHelper;
        
        protected $resultFactory;


        public function __construct(
                Context $context,
                \Magento\Framework\Session\SessionManagerInterface $coreSession,
                array $data = [])
        {
        parent::__construct($context);
        $this->coreSession = $coreSession;
        }

        public function execute()
	{
            
            $sampleProductId =  $this->getRequest()->getPost('productId');    
            $sampleProductImage  = $this->getRequest()->getPost('productImage');
            $sampleProductName  = $this->getRequest()->getPost('productName');    
            $this->coreSession->start();    
            $swatchsession = $this->coreSession->getHomescapessampleswatch();
            $maincount=0;
            $reminingcount=5;
            $msg = '';
            
            //var_dump($swatchsession); die('ttt');
            
            if($swatchsession!='')
            {
                
                $sampleswatchsession=explode(',',$swatchsession);
                $maincount=count($sampleswatchsession);
                $reminingcount=5-count($sampleswatchsession);
                if(count($sampleswatchsession)<5)
                {
                    if(!empty($swatchsession))
                    {
                        if (in_array($sampleProductId,$sampleswatchsession))
                        {       
                            $msg= "You have already this sample in request";
                        }
                      else
                      {  
                        $finalSession=$this->coreSession->getHomescapessampleswatch().",".$sampleProductId;     
                        $this->coreSession->setHomescapessampleswatch($finalSession);

                      }
                    }
                    else
                    {
                        $this->coreSession->setHomescapessampleswatch($sampleProductId);
                        
                    } 
                    
                }
                else
                {
                    $msg= "You have already 5 sample in request";
                }
                
                
                
            }else{
                $this->coreSession->start();  
                $this->coreSession->setHomescapessampleswatch($sampleProductId);            
            }
            
            $ic=$maincount+1;
        
            $this->coreSession->start();  
            $swatchsession = $this->coreSession->getHomescapessampleswatch(); 
            
            
            $html='<li id="updatedswatcheslist'.$ic.'"><a class="remove-swatch rswatch'.$sampleProductId.'" data-id="'.$sampleProductId.'" href="javascript: void(0)" >&nbsp;</a><img src="'.$sampleProductImage.'" alt="Product Image" width="50" height="50"/><p class="desc">'.$sampleProductName.'</p><span class="remove-wait1 remove-wait'.$sampleProductId.'" style="display:none">&nbsp;</span></li>';

            $arrayjson=array('html'=>$html,'reminihtml'=>$swatchsession,'msg'=>$msg,'productnumber'=>$maincount);
            
             
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($arrayjson);
            return $resultJson; 
		
	}
}
