<?php

namespace Homescapes\Career\Controller\Save;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Filesystem\DirectoryList;


class Index extends Action
{
    protected $resultPageFactory;
    protected $_helper;
    protected $_filesystem;
    protected $_fileUploaderFactory;
    protected $fileId = 'cv';
    protected $_storeManager;
    protected $_transportBuilder;


    public function __construct(Context $context, 
         \Homescapes\Career\Helper\Data $helper, 
         \Magento\Framework\Filesystem $_filesystem,
         \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory, 
         PageFactory $pageFactory,
         \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder)
    {
        $this->resultPageFactory = $pageFactory;
        
        parent::__construct($context);
        $this->_helper = $helper;
        $this->_filesystem = $_filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_storeManager = $storeManager;
        $this->_transportBuilder= $transportBuilder;
    }

    public function execute()
    {

        $title = $this->_helper->getTitle();

        if(!$title){
          $title = 'Career';
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set($title);

         if ($data = $this->getRequest()->getPost()) 
         {
            
            if(isset($_FILES['cv']['name']) and (file_exists($_FILES['cv']['tmp_name'])))
            {

                  $target = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mycvupload/');

                  if(!file_exists($target))
                  { mkdir($target, 0777, true); }

                      try {

                            $myfileupload = $_FILES['cv']['name'];
                            $uploader = $this->_fileUploaderFactory->create(['fileId' => $this->fileId ]);
                      
                            $uploader->setAllowedExtensions(array('doc','docx','xlsx','pdf'));
                            $uploader->setAllowCreateFolders(true);
                            $uploader->setAllowRenameFiles(false);
                            $uploader->setFilesDispersion(false);
                            $result = $uploader->save($target);
                            $filePath = $result['path'].$result['file'];
                            $fileName = $result['name'];
                           
                          } catch (\Exception $e) {
                               $this->messageManager->addError($e->getMessage());
                          }

                    $templateId = $this->_helper->getTemplateId();
                    $senderEmail= trim($this->_helper->getOwnerEmail());
                    $senderName="Homescapes";
                    $customerEmail= trim($data['email_address']);
                    $fullName=$data['fname']." ".$data['lname'];

                    $email_template_variables = array(               
                       'name' => $senderName,
                       'fullname' => $fullName,
                       'phone' => $data['phone'],
                       'job' => $data['job'],
                       'jobtitle' => $data['jobtitle'],
                       'email' => $data['email_address'],
                       'user' => ''

                      );  

                   
                  $email_template_variables_admin = array(               
                     'name' => $senderName,
                     'fullname' => $fullName,
                     'phone' => $data['phone'],
                     'job' => $data['job'],
                     'jobtitle' => $data['jobtitle'],
                     'email' => $data['email_address'], 
                     'user' => 'admin'
                    );           
                 
                  $sender = [
                    'name' => $senderName,
                    'email' => $senderEmail,
                  ];

                  try{
                      /* for customer */
                      $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
                      ->setTemplateOptions([
                          'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                          'store' => $this->_storeManager->getStore()->getId(),
                      ])->setTemplateVars($email_template_variables)->setFrom($sender)->addTo($customerEmail, $fullName)->getTransport();
                      $transport->sendMessage();
                      
                      /* for admin */ 
                      
                      $transportadmin = $this->_transportBuilder->setTemplateIdentifier($templateId)
                      ->setTemplateOptions([
                          'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                          'store' => $this->_storeManager->getStore()->getId(),
                      ])->setTemplateVars($email_template_variables_admin)->setFrom($sender)->addTo($senderEmail, $senderName)->addAttachment($filePath, $fileName)->getTransport();
                      $transportadmin->sendMessage();
                      $this->messageManager->addSuccess(__('Thank you. Your Cv has uploaded successfully. We will contact you very soon.')); 
                  }catch (\Exception $e) {
                               $this->messageManager->addError('There was a problem to send email.');
                  }  
                  
            } // close isset if

            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('career');
            return $resultRedirect;

        } // close $data if        
        
        return $resultPage;
    }
}