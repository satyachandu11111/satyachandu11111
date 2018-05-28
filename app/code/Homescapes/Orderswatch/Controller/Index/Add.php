<?php

namespace Homescapes\Orderswatch\Controller\Index;

use Magento\Framework\App\Action\Action;

class Add extends Action
{
    protected $orderSwatchFactory;
    
    protected $datetime;
    
    protected $_storeManager;    
    
    protected $_helper;
    
    protected $_transportBuilder;
        
    public function __construct(\Magento\Framework\App\Action\Context $context,
            \Homescapes\Orderswatch\Model\OrderswatchFactory $orderSwatchFactory,
            \Magento\Store\Model\StoreManagerInterface $storeManager,        
            \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
            \Homescapes\Orderswatch\Helper\Data $helper,
            \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder) {
        
        parent::__construct($context);
        $this->orderSwatchFactory = $orderSwatchFactory;
        $this->datetime = $datetime;
        $this->_storeManager = $storeManager;
        $this->_transportBuilder= $transportBuilder;        
        $this->_helper = $helper;
    }

        public function execute() {
            
        $data=$this->getRequest()->getPost();        
        $customData = array();
        if($data){
            $orderSwatchModel = $this->orderSwatchFactory->create();
            $storeId = $this->_storeManager->getStore()->getId();
            try{
                
                $data['meainpsku']=$data['productsku'];
//                $data['product_sku']=implode(",",$data['productsku']);
//                $data['requestproduct_id']=implode(",",$data['productid']); 
                $data['store_id']=$storeId;
                $data['address']=trim($data['address']);
                $data['address2']=trim($data['address2']);
                $data['send_date']= $date = $this->datetime->gmtDate();               
                
                $customData['product_sku']=implode(",",$data['productsku']);;
                $customData['requestproduct_id']= implode(",",$data['productid']); 
                $customData['store_id']=$data['store_id'];
                $customData['address']=$data['address']; 
                $customData['address2']=$data['address2']; 
                $customData['send_date']=$data['send_date']; 
                $customData['fname']=$data['fname']; 
                $customData['lname']=$data['lname']; 
                $customData['email_address']=$data['email_address']; 
                $customData['city']=$data['city']; 
                $customData['county']=$data['county']; 
                $customData['country']=$data['country']; 
                $customData['productsku']=$data['productsku']; 
                $customData['productid']=$data['productid']; 
                $customData['productname']=$data['productname']; 
                $customData['zip_code']=$data['zip_code']; 
                $customData['meainpsku']=$data['meainpsku'];
                $customData['productname']=$data['productname'];
                
                
                $orderSwatchModel->setData($customData);                
                $orderSwatchModel->save();
                //var_dump($orderSwatchModel->getId()); die('sssss');
                $this->_helper->setLastswatchId($orderSwatchModel->getId());
                
                //$this->sendEmail($customData);
                
                $this->_helper->setHomescapessampleswatch('');
                $this->_redirect('*/*/success');
                $this->messageManager->addSuccess(__('Thank you. Your order for free swatches has been placed.'));
                
            }catch (\Exception $e) {
            
                $this->messageManager->addError(__($e->getMessage()));
                $this->_redirect('*/*/index');
               return;
            }
        }
        
    }
    
    public function sendEmail($data){
        $templateId = $this->_helper->getSystemValues('orderswatch/general/user_email_templates');
                
//       // $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
//        $emailTemplate = Mage::getModel('core/email_template')->load($templateId);
       
        $senderEmail = $this->_helper->getSystemValues('orderswatch/general/owner_email');
        $senderbccEmail = $this->_helper->getSystemValues('orderswatch/general/ownerbcc_email');
        //Getting the Store E-Mail Sender Name.
        $senderName="Homescapes";
        $sender = [
                    'name' => $senderName,
                    'email' => $senderEmail,
                  ];
        $customerEmail=$data['email_address'];
        
        
        $emailTemplateVariables = array();
        $emailTemplateVariables['swatchuser'] = $customerName;
        $emailTemplateVariables['email'] = $customerEmail;
        $fullName=$data['fname']." ".$data['lname'];
        
        foreach ($data['meainpsku'] as $key => $value) 
        {
           $producthtml.='<tr><td style="width:50%;height:22px;">';
           $producthtml.=$data['productname'][$k];
		   $producthtml.='</td><td width="10">&nbsp;</td><td style="width:20%;height:22px;">';
           $producthtml.=$value.' - Sample';
           $producthtml.='</td></tr>';
           $k++;
        }
        
        $email_template_variables = array(
           'swatchuser' => $fullName,
           'name' => $fullName,
           'address' => $data['address'],
           'city' => $data['city'],
           'zip' => $data['zip_code'],
           'county' => $data['county'],
           'country' => $data['country'],
           'order'=>$producthtml
        );    
        
         try{
        
                $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
                      ->setTemplateOptions([
                          'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                          'store' => $this->_storeManager->getStore()->getId(),
                      ])->setTemplateVars($email_template_variables)->setFrom($sender)->addTo($customerEmail, $fullName)->getTransport();
                      $transport->sendMessage();
                      
                return true;      
                      
            }catch (\Exception $e) {
                               $this->messageManager->addError('There was a problem to send email.');
                  }  
        
    }
}
