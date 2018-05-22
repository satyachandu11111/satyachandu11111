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

             
              if ($result['file']) {
                     $this->messageManager->addSuccess(__('File has been successfully uploaded')); 
                 }
            } catch (\Exception $e) {
                 $this->messageManager->addError($e->getMessage());
            }

            
                $templateId = $this->_helper->getTemplateId();
                $senderEmail= $this->_helper->getOwnerEmail();
                $senderName="Homescapes";
               // $customerName = $data['fname']; 
                $customerEmail=$data['email_address'];

                
                //$emailTemplate = Mage::getModel('core/email_template')->load($templateId);               
                

                $email_template_variables = array();
                $email_template_variables['swatchuser'] = $data['fname'];
                $email_template_variables['email'] = $customerEmail;
                
                $fullName=$data['fname']." ".$data['lname'];

                $email_template_variables = array(               
                   'name' => $fullName,
                   'phone' => $data['phone'],
                   'job' => $data['job'],
                   'jobtitle' => $data['jobtitle']              
                );  

              $jobform='<table width="100%"><tr><td colspan="2">Candidate Details</td></tr><tr><td>Name</td><td>'.$fullName.'</td></tr><tr><td>Email Id</td><td>'.$customerEmail.'</td></tr><tr><td>Contact Number</td><td>'.$data['phone'].'</td></tr><tr><td> Job Title</td><td>'.$data['job'].'</td></tr></table>';
              $email_template_variables_admin = array();
              $email_template_variables_admin['swatchuser'] = $data['fname'];
              $email_template_variables_admin['email'] = $customerEmail;

              $fullName=$data['fname']." ".$data['lname'];

              $email_template_variables_admin = array(               
                 'name' => $senderName,
                 'phone' => $data['phone'],
                 'job' => $data['job'],
                 'jobtitle' => $data['jobtitle'],
                 'jobform'  =>$jobform
                );           
            //Appending the Custom Variables to Template.

            $processedTemplate = $this->_transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->_storeManager->getStore()->getId(),
            ])->setTemplateVars($email_template_variables);

            $processedTemplateadmin = $this->_transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->_storeManager->getStore()->getId(),
            ])->setTemplateVars($email_template_variables_admin);

            }
            try { 

                  $mail = new \Zend_Mail();
                  $mail->setType(\Zend_Mime::MULTIPART_RELATED);
                  $mail->setBodyHtml($processedTemplate);
                  $mail->setFrom($senderName,$senderEmail);
                  $mail->addTo($customerEmail,$fullName);
                  $mail->setSubject('Subject');                 
                  $mail->send(); 

                  $mails = new \Zend_Mail();
                  $mails->setType(\Zend_Mime::MULTIPART_RELATED);
                  $mails->setBodyHtml($processedTemplateadmin);
                  $mails->setFrom($senderName,$senderEmail);
                  $mails->addTo($senderEmail,$senderName);
                  $mails->setSubject('Subject :');
                  $attachFileHeremails = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mycvupload/'.$myfileupload);   
                  $fileNamemails = $myfileupload;
                  $filemails = $mails->createAttachment(file_get_contents($attachFileHeremails));
                  $filemails->type   = 'application/doc';
                  $filemails->disposition = \Zend_Mime::DISPOSITION_INLINE;
                  $filemails->encoding    = \Zend_Mime::ENCODING_BASE64;
                  $filemails->filename    = $fileNamemails;
                  $mails->send();            

                   $this->messageManager->addSuccess(__('File has been successfully uploaded')); 
                } 
                catch (\Exception $e) {
                   $this->messageManager->addError($e->getMessage());
                }

            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('career/index/index');
            return $resultRedirect;

        }        
        
        return $resultPage;
    }
}