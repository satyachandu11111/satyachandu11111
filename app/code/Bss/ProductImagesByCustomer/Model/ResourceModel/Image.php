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
namespace Bss\ProductImagesByCustomer\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use \Magento\Framework\Message\ManagerInterface;

class Image extends AbstractDb
{

    /**
     * ID ALL STORE VIEW
     */
    const ID_ALL_STORE_VIEW = 0;

    /**
     * APPROVE
     */
    const APPROVE = 1;

    /**
     * ResourceConnection
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resources;

    /**
     * AdapterInterface
     *
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connections;

    /**
     * Message Manager
     * @var ManagerInterface
     */
    protected $messageManager;

    protected $mainTableName;

    /**
     * Image constructor.
     * @param Context $context
     * @param ManagerInterface $messageManager
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        ManagerInterface $messageManager,
        $connectionName = null
    ) {
        $this->messageManager = $messageManager;
        $this->resources = $context->getResources();
        $this->connections = $this->resources->getConnection();
        parent::__construct($context, $connectionName);
    }

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        
        $this->mainTableName = $this->getTable('bss_images_customer_upload');
        $this->_init($this->mainTableName, 'bss_image_customer_upload_id');
    }

    /**
     * Update images database
     * @param array $bind
     * @param array $where
     * @return void
     */
    public function updateImagesDataBase($bind = [],$where = [])
    {
        if (is_array($bind) && is_array($where)) {
            try {
                $this->connections->update($this->mainTableName, $bind, $where);
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
    }

    /**
     * Insert images database
     *
     * @param array $bind
     * @return void
     */
    public function insertImagesDataBase($bind = [])
    {
        if (is_array($bind)) {
            try {
                $this->connections->insert($this->mainTableName, $bind);
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
    }

    /**
     * Check Image Has Store View Current
     *
     * @var string $stringStoreView
     * @var int $idStoreView
     * @return bool
     **/
    protected function checkImageHasStoreViewCurrent($stringStoreView, $idStoreCurrent)
    {
        $checkStoreView = false;

        $arrStoreViewInImage = explode(',', $stringStoreView);
        if (
            in_array($idStoreCurrent, $arrStoreViewInImage) ||
            in_array(self::ID_ALL_STORE_VIEW, $arrStoreViewInImage)
        ) {
            $checkStoreView = true;
        }

        return $checkStoreView;
    }

    /**
     * Get Images DataBase By Product
     *
     * @param int $productCode
     *
     * @return array
     */
    public function getImagesDataBaseByProduct($productCode, $storeViewId)
    {
        try {
            //Get all images by product sku
            $sql = $this->connections->select()->from(
                [$this->mainTableName],
                [
                    "link_image",
                    "id_store"
                ]
            )->where(
                "id_product = ?", $productCode
            )->where(
                "approve = ?", self::APPROVE
            );

            $arrLinkImages = [];

            $query = $this->connections->query($sql);

            while ($row = $query->fetch()) {
                array_push($arrLinkImages, $row);
            }

        }
        catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        $arrImageDisplay = [];
        //ArrLinkImages approve in product code
        if ( !empty($arrLinkImages) ) {
            foreach ($arrLinkImages as $image) {
                if (
                $this->checkImageHasStoreViewCurrent(
                    $image["id_store"],
                    $storeViewId)
                ) {
                    $imageAdd = [
                        "link_image" => $image["link_image"]
                    ];
                    array_push($arrImageDisplay, $imageAdd);
                }
            }
        }

        return $arrImageDisplay;
    }
}
