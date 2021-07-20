<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ProductImagesByCustomer
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductImagesByCustomer\Controller\Index;

use \Magento\MediaStorage\Model\File\UploaderFactory;
use \Magento\Framework\App\Action;
use \Magento\Framework\Filesystem;
use \Magento\Framework\App\Request\Http;
use Bss\ProductImagesByCustomer\Model\ResourceModel\ImageFactory;
use Bss\ProductImagesByCustomer\Helper\Data;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\Message\ManagerInterface;
use Bss\ProductImagesByCustomer;
use Magento\Framework\Data\Form\FormKey\Validator;
use Bss\ProductImagesByCustomer\Helper\ConfigAdmin;
use \Magento\Framework\Stdlib\DateTime\DateTime;
use Bss\ProductImagesByCustomer\Helper;

class SaveImages extends \Magento\Framework\App\Action\Action
{

    /**
     * Constant ENABLE
     */
    const ENABLE = 1;

    /**
     * Constant DISABLE
     */
    const DISABLE = 0;

    /**
     * Helper Email
     * @var Helper\Email
     */
    protected $helperEmail;

    /**
     * Date
     * @var DataTime
     */
    protected $date;

    /**
     * Helper config admin
     * @var ConfigAdmin
     */
    protected $helperConfigAdmin;

    /**
     * FormKey Validator
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * Helper Product images customer
     * @var ProductImagesByCustomer\Helper\CustomerLogin
     */
    protected $helperCustomerLogin;

    /**
     * Helper
     * @var Data
     */
    protected $helper;

    /**
     * Image Factory
     * @var ImageFactory
     */
    protected $imageFactory;

    /**
     * File system
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * UploaderFactory
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * HTTP
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * Message Manager interface
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * SaveImages constructor.
     * @param Action\Context $context
     * @param Filesystem $fileSystem
     * @param UploaderFactory $fileUploaderFactory
     * @param Http $request
     * @param ImageFactory $imageFactory
     * @param Data $helper
     * @param Helper\CustomerLogin $helperCustomerLogin
     * @param Validator $formKeyValidator
     * @param DateTime $date
     * @param Helper\Email $helperEmail
     * @param ConfigAdmin $helperConfigAdmin
     */
    public function __construct(
        Action\Context $context,
        Filesystem $fileSystem,
        UploaderFactory $fileUploaderFactory,
        Http $request,
        ImageFactory $imageFactory,
        Data $helper,
        ProductImagesByCustomer\Helper\CustomerLogin $helperCustomerLogin,
        Validator $formKeyValidator,
        DateTime $date,
        Helper\Email $helperEmail,
        ConfigAdmin $helperConfigAdmin
    ) {
        $this->fileSystem = $fileSystem;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->request = $request;
        $this->imageFactory = $imageFactory;
        $this->helper = $helper;
        $this->helperCustomerLogin = $helperCustomerLogin;
        $this->messageManager = $context->getMessageManager();
        $this->formKeyValidator = $formKeyValidator;
        $this->helperConfigAdmin = $helperConfigAdmin;
        $this->date = $date;
        $this->helperEmail = $helperEmail;
        parent::__construct($context);
    }

    /**
     * Insert Images By Customer
     * @param array $bind
     * @return void
     */
    protected function insertImageByCustomer($bind = [])
    {
        $this->imageFactory->create()->insertImagesDataBase($bind);
    }

    /**
     * Save Image Uploader
     * @param UploaderFactory $uploader
     * @param string $target
     * @return array
     */
    protected function saveImageUploader($uploader, $target)
    {
        return $uploader->save($target);
    }

    /**
     * Execute
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ( !$this->formKeyValidator->validate($this->getRequest())) {
            $resultRedirect = $this->resultFactory->create(
                \Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT
            );
            return $resultRedirect->setPath('no-route');
        } else {
            try {
                $data = $this->getRequest()->getPostValue();

                $mediaDir = $this->fileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                $target = $mediaDir->getAbsolutePath('/bss/productimagesbycustomer/');

                $store = $this->helper->getStoreView();
                $customerName='';

                if ($this->helperConfigAdmin->configEmailRequired()) {
                    $emailCustomer = isset($data["bssCustomerEmail"])?$data["bssCustomerEmail"]: $this->helperCustomerLogin->getEmailCustomer();
                    $customerName=isset($data["bssCustomerName"])?$data["bssCustomerName"]: $this->helperCustomerLogin->getNameCustomer();
                } else if (
                    !$this->helperConfigAdmin->configGuestUploadImage() &&
                    $this->helperCustomerLogin->checkCustomerLogined()
                ) {
                    $emailCustomer = $this->helperCustomerLogin->getEmailCustomer();
                    $customerName== $this->helperCustomerLogin->getNameCustomer();
                } else {
                    $emailCustomer = isset($data["bssCustomerEmail"])?$data["bssCustomerEmail"]:"";
                    $customerName=isset($data["bssCustomerName"])?$data["bssCustomerName"]: "";
                }

                //Cutomer Message : 
                $customerMessage=isset($data["bssCustomerMessage"])?$data["bssCustomerMessage"]: "";

                //Image disable
                $approve = self::DISABLE;
                $productId = $data['skuProductImages'];

                //Upload images
                for ($i = 0; $i < $data["numberFileUpload"]; $i++) {
                    $fileId = 'bssUploadImage' . $i;

                    if ($this->request->getFiles($fileId)['size'] != 0) {
                        $uploader = $this->fileUploaderFactory->create(['fileId' => $fileId]);
                        $uploader->setAllowedExtensions(
                            [
                                'jpg',
                                'jpeg',
                                'gif',
                                'png',
                            ]
                        );
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);
                        $uploader->setAllowCreateFolders(true);

                        $dataImage = $this->saveImageUploader($uploader, $target);

                        $linkImage = $dataImage["file"];
                        $bind = [
                            'link_image' => $linkImage,
                            'email_customer' => $emailCustomer,
                            'id_store' => $store,
                            'id_product' => $productId,
                            'approve' => $approve,
                            'customer_name' => $customerName,
                            'customer_message' => $customerMessage,
                            'customer_date' => date('Y-m-d',time())
                            
                        ];


                        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info(print_r(time(),1));
                        $this->insertImageByCustomer($bind);
                    }

                }

                //Send Email
                if ($this->helperConfigAdmin->configEnableEmail() ) {
                    //Send Email Admin
                    $timeSender = $this->date->gmtDate();
                    $emailReceiver = $this->helperConfigAdmin->configEmailReceiver();
                    $emailReceiver = str_replace(' ', '', $emailReceiver);

                    $emailTemplate = $this->helperConfigAdmin->configEmailTemplate();
                    $templateVar = [
                        'nameCustomer' => $data["bssCustomerName"],
                        'numberImages' => $data["numberFileUpload"],
                        'productCode' => $productId,
                        'varEmail' => $emailCustomer,
                        'time' => $timeSender,
                    ];
                    $this->helperEmail->sendEmail($emailReceiver, $emailTemplate, $templateVar);
                }
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
            if ($data["numberFileUpload"] > 0) {
                $this->messageManager->addSuccessMessage('You have successfully uploaded images. Please wait for approval!');
            } else {
                $this->messageManager->addSuccessMessage('You have successfully uploaded an image. Please wait for approval!');
            }
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

            return $resultRedirect;
        }
    }
}
