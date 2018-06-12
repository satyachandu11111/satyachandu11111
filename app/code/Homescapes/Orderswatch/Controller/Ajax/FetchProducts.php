<?php
namespace Homescapes\Orderswatch\Controller\Ajax;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;


class FetchProducts extends \Magento\Framework\App\Action\Action
{
    
    protected $coreSession;
        
    protected $jsonHelper;

    protected $resultFactory;


    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Framework\Session\SessionManagerInterface $coreSession,
            array $data = [])
    {
    parent::__construct($context);
    $this->coreSession = $coreSession;
    }

    public function execute()
    {
        //$this->coreSession->start();    
        $swatchsession = $this->coreSession->getHomescapessampleswatch();
        
        
        $arrayjson=array('selectedProducts'=>$swatchsession);
        
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($arrayjson);
        return $resultJson; 

    }
    
}
