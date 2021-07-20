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
namespace Bss\ProductImagesByCustomer\Controller\Adminhtml\Image;

use Magento\Backend\App\Action;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Backend\Model\Session;
use Bss\ProductImagesByCustomer\Model\ImageFactory;
use Bss\ProductImagesByCustomer\Model\ResourceModel;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\Exception\LocalizedException;

class Save extends Action
{

    /**
     * ProductFactory
     *
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * CollectionFactory
     *
     * @var CollectionFactory
     */
    protected $collectionProductFactory;

    /**
     * ResourceConnection
     *
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * ScopeConfigInterface
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Image Factory
     *
     * @var imageFactory
     */
    protected $imageFactory;

    /**
     * SessionFactory
     *
     * @var Session
     */
    protected $session;

    /**
     * ResourceImageFactory
     *
     * @var ResourceModel\ImageFactory
     */
    protected $resourceImageFactory;

    /**
     * Save constructor.
     * @param CollectionFactory $collectionProductFactory
     * @param Action\Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductFactory $productFactory
     * @param ResourceConnection $resource
     * @param ImageFactory $imageFactory
     * @param ResourceModel\ImageFactory $resourceImageFactory
     */
    public function __construct(
        CollectionFactory $collectionProductFactory,
        Action\Context $context,
        ScopeConfigInterface $scopeConfig,
        ProductFactory $productFactory,
        ResourceConnection $resource,
        ImageFactory $imageFactory,
        ResourceModel\ImageFactory $resourceImageFactory
    ) {
        parent::__construct($context);
        $this->collectionProductFactory = $collectionProductFactory;
        $this->productFactory = $productFactory;
        $this->scopeConfig = $scopeConfig;
        $this->resource = $resource;
        $this->session = $context->getSession();
        $this->imageFactory = $imageFactory;
        $this->resourceImageFactory = $resourceImageFactory;
    }

    /**
     * Save Image DataBase
     * @param array $bind
     */
    protected function saveImageDataBase($bind = [])
    {
        $this->resourceImageFactory->create()->insertImagesDataBase($bind);
    }

    /**
     * Convert Array To String
     * @param $array
     * @return array | string
     */
    protected function convertArrayToString($array = [])
    {
        if (is_array($array)) {
            $string = implode(",", $array);
            return $string;
        } else {
            return $array;
        }

    }

    /**
     * Save
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->imageFactory->create();
            $id = $this->getRequest()->getParam('bss_image_customer_upload_id');
            if ($id) {
                try {
                    $model->load($id);
                    $model->setApprove($data["approve"]);
                    $model->setStoreView($this->convertArrayToString($data["id_store"]));
                    $model->save();
                    $this->messageManager->addSuccessMessage(__('The image has been saved.'));
                    $this->session->setFormData(false);
                    if ($this->getRequest()->getParam('back')) {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            [
                                'bss_image_customer_upload_id' => $model->getId(),
                                '_current' => true
                            ]
                        );
                    }
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage($e, __($e->getMessage()));
                }
                return $resultRedirect->setPath('*/*/');
            }
            try {
                if ( (!isset($id)) && isset($data["bss-productimagesbycustomer"]["images"]) ) {
                    foreach ($data["bss-productimagesbycustomer"]["images"] as $image) {
                        $bind = [
                            'link_image' => $image["file"],
                            'email_customer' => $data["email_customer"],
                            'id_store' => $this->convertArrayToString($data["id_store"]),
                            'id_product' => $data["id_product"],
                            'customer_name' => $data["customer_name"],
                            
                            'approve' => $data["approve"]
                        ];
                        //Save image data base
                        $this->saveImageDataBase($bind);
                    }
                    //Save finish redirect before url
                    $this->messageManager->addSuccessMessage(__('The image has been saved.'));
                    return $resultRedirect->setPath('*/*/');
                } elseif ((!isset($id)) && (!isset($data["bss-productimagesbycustomer"]["images"]))) {
                    $resultRedirect = $this->resultFactory->create(
                        ResultFactory::TYPE_REDIRECT
                    );
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    $this->messageManager->addErrorMessage(__('No images choose.'));
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __($e->getMessage()));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'bss_image_customer_upload_id' => $this->getRequest()->getParam('bss_image_customer_upload_id')
                ]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check Rule
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed("Bss_ProductImagesByCustomer::save");
    }
}
