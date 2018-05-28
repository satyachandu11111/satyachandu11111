<?php
namespace Homescapes\Orderswatch\Controller\Ajax;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class RemoveProductSample extends \Magento\Framework\App\Action\Action
{
	protected $coreSession;
        
        protected $jsonHelper;
        
        protected $resultFactory;


        public function __construct(
                Context $context,
                \Magento\Framework\Session\SessionManagerInterface $coreSession,
                array $data = [])
        {
        parent::__construct($context,$data);
        $this->coreSession = $coreSession;
        }

        public function execute()
	{
            
            $sampleProductId=$this->getRequest()->getParam('productId');        
            
            $swatchsession = $this->coreSession->getHomescapessampleswatch();

            if($swatchsession!='')
            {
                $sampleswatchsession=explode(',',$swatchsession);
                if (in_array($sampleProductId,$sampleswatchsession))
                {

                    $pos=array_search($sampleProductId,$sampleswatchsession);
                    $sampleswatchsession = array_diff($sampleswatchsession, array($sampleProductId));
                }
                $finalRemovelist=implode(",", $sampleswatchsession);
                $productnumber=$pos+1;
                $this->coreSession->setHomescapessampleswatch($finalRemovelist);
                
                $html='<li class="blankswatch"><p class="emptydesc">'.__('Add Another Swatch').'</p></li>';
            }

            $arrayjson=array('html'=>$html,'reminihtml'=>$finalRemovelist,'productnumber'=>$productnumber);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($arrayjson);
            return $resultJson; 
		
	}
}

