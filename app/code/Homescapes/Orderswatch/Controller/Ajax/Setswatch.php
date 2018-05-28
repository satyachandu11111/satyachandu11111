<?php
namespace Homescapes\Orderswatch\Controller\Ajax;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Setswatch extends \Magento\Framework\App\Action\Action
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
            
            $flag =  $this->getRequest()->getPost('flag');    
            $this->coreSession->setHomescapessampleswatchclose($flag);            
            $arrayjson=array('html'=>'true');
             
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($arrayjson);
            return $resultJson; 
		
	}
}

