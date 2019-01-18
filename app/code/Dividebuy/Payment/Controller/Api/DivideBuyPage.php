<?php

namespace Dividebuy\Payment\Controller\Api;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class DivideBuyPage extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     *  @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

     /**
     * @var \Dividebuy\Payment\Helper\Data
     */
    protected $_paymentHelper;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_divideBuylogger;

     /**
     * @var \Magento\Store\Model\ScopeInterface
     */
    protected $_scopeConfig;

	/**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    protected $_pageRepository;

    /**
     * @param \Magento\Framework\Json\Helper\Data        $jsonHelper
     * @param Context                                    $context
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Dividebuy\Payment\Helper\Data $paymentHelper,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        \Dividebuy\RetailerConfig\Logger\Logger $divideBuylogger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
        ) {
        $this->_jsonHelper           = $jsonHelper;
        $this->configWriter = $configWriter;
        $this->_paymentHelper        = $paymentHelper;
        $this->_pageFactory = $pageFactory;
        $this->_pageRepository = $pageRepository;
        $this->_divideBuylogger         = $divideBuylogger;
        $this->_scopeConfig          = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Used to create dividebuy page
     */
    public function execute()
    {
        $post     = trim(file_get_contents("php://input"));
        $postData = $this->_jsonHelper->jsonDecode($post);
        //if data is blank sending error response
        if(empty($postData)){
            $result = array("error" => 1, "success" => 0, "message" => "There is a problem in retriving data","status"=>"406");
            /* For error log code start*/
            $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,empty($storeId) ? 0 : $storeId);
            if($errorLogStatus == 1)
            {
                $dataResponse = "============\n";
                $dataResponse .= "Url : Gurantor api there is a problem in retriving the data\n";
                $dataResponse .= "Param :".$post."\n";
                $dataResponse .= "Response :".json_encode($result)."\n";
                $this->_divideBuylogger->info($dataResponse);    
            }
            /* Error log code end*/
            $this->_paymentHelper->_prepareDataJSON($result);
            return;
        }
        //Creating cms page and if identifier is there than it will update the page
        $identifier = 'dividebuy-interestfree-credit';
        //getting the store id
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();        
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $store_id = $storeManager->getStore()->getStoreId();

        $cmsPage = $this->_pageFactory->create();
        $cmsPage = $cmsPage->load($identifier);
        $cmsPage->setIdentifier($identifier)
            ->setTitle($postData["title"])
            ->setContentHeading('')
            ->setContent($postData["content"])
            ->setPageLayout('1column')
            ->setData('stores', [ $store_id ]);

        $this->_pageRepository->save($cmsPage);

        //Giving the response back
        $result = array("error" => 0, "success" => 1, "message" => "ok","status"=>"200");
        /* For error log code start*/
        $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,empty($storeId) ? 0 : $storeId);
        if($errorLogStatus == 1)
        {
            $dataResponse = "============\n";
            $dataResponse .= "Url : Gurantor api there is a problem in retriving the data\n";
            $dataResponse .= "Param :".$post."\n";
            $dataResponse .= "Response :".json_encode($result)."\n";
            $this->_divideBuylogger->info($dataResponse);    
        }
        /* Error log code end*/
        $this->_paymentHelper->_prepareDataJSON($result);
        return;
    }
}



