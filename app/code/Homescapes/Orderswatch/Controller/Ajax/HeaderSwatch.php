<?php
namespace Homescapes\Orderswatch\Controller\Ajax;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;


class HeaderSwatch extends \Magento\Framework\App\Action\Action
{
    
    protected $coreSession;
        
    protected $jsonHelper;

    protected $_resultPageFactory;


    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Magento\Framework\Session\SessionManagerInterface $coreSession,
            array $data = [])
    {
    parent::__construct($context,$data);
    $this->coreSession = $coreSession;
    $this->_resultPageFactory = $resultPageFactory;		
    }

    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $swatchsession = $this->coreSession->getHomescapessampleswatch();
        
        $html = $resultPage->getLayout()->createBlock('Homescapes\Orderswatch\Block\Swatchlist')->setTemplate('Homescapes_Orderswatch::sample/headerswatchajax.phtml')->toHtml();
        
        $arrayjson=array('html'=>$html);
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($arrayjson);
        return $resultJson; 

    }
    
}

