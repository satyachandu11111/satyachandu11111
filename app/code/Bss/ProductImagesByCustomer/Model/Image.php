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
namespace Bss\ProductImagesByCustomer\Model;

use \Magento\Framework\Model\AbstractModel;
use \Bss\ProductImagesByCustomer\Api\Data\ImageInterface;

class Image extends AbstractModel implements ImageInterface
{
    /**
     * Construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Bss\ProductImagesByCustomer\Model\ResourceModel\Image');
    }

    /**
     * Set Id Image
     * @param int $imageId
     * @return void
     */
    public function setIdImage($imageId)
    {
        $this->setData(self::BSS_ID_IMAGE, $imageId);
    }

    /**
     * Get Id Image
     * @return int
     */
    public function getIdImage()
    {
        return $this->getData(self::BSS_ID_IMAGE);
    }


    /**
     * Set approve
     * @param int $approve
     * @return void
     */
    public function setApprove($approve)
    {
        $this->setData(self::BSS_APPROVE, $approve);
    }

    /**
     * Get approve
     * @return int
     */
    public function getApprove()
    {
        return $this->getData(self::BSS_APPROVE);
    }

    /**
     * Set product of image
     * @param int $idProduct
     * @return void
     */
    public function setProductImage($idProduct)
    {
        $this->setData(self::BSS_ID_PRODUCT, $idProduct);
    }

    /**
     * Get product of image
     * @return int
     */
    public function getProductImage()
    {
        return $this->getData(self::BSS_ID_PRODUCT);
    }

    /**
     * Set Link Image
     * @param string $link
     * @return void
     */
    public function setLinkImage($link)
    {
        $this->setData(self::BSS_LINK_IMAGE, $link);
    }

    /**
     * Get Link Image
     * @return string
     */
    public function getLinkImage()
    {
        return $this->getData(self::BSS_LINK_IMAGE);
    }

    /**
     * Set email customer
     * @param string $email
     * @return void
     */
    public function setEmailCustomer($email)
    {
        $this->setData(self::BSS_EMAIL_CUSTOMER, $email);
    }

    /**
     * Get email customer
     * @return string
     */
    public function getEmailCustomer()
    {
        return $this->getData(self::BSS_EMAIL_CUSTOMER);
    }

    /**
     * Set store view
     * @param int $idStore
     * @return void
     */
    public function setStoreView($idStore)
    {
        $this->setData(self::BSS_ID_STORE, $idStore);
    }

    /**
     * Get store view
     * @return int
     */
    public function getStoreView()
    {
        return $this->getData(self::BSS_ID_STORE);
    }
}
