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
namespace Bss\ProductImagesByCustomer\Api\Data;

interface ImageInterface
{

    /**
     * BSS_ID_IMAGE
     */
    const BSS_ID_IMAGE = 'bss_image_customer_upload_id';

    /**
     * BSS_APPROVE
     */
    const BSS_APPROVE = 'approve';

    /**
     * BSS_ID_PRODUCT
     */
    const BSS_ID_PRODUCT = 'id_product';

    /**
     * BSS_LINK_IMAGE
     */
    const BSS_LINK_IMAGE = 'link_image';

    /**
     * BSS_EMAIL_CUSTOMER
     */
    const BSS_EMAIL_CUSTOMER = 'email_customer';

    /**
     * BSS_ID_STORE
     */
    const BSS_ID_STORE = 'id_store';

    /**
     * Set Id Image
     * @param int $imageId
     * @return void
     */
    public function setIdImage($imageId);

    /**
     * Get Id Image
     * @return int
     */
    public function getIdImage();

    /**
     * Set Approve
     * @param int $approve
     * @return void
     */
    public function setApprove($approve);

    /**
     * Get Approve
     * @return int
     */
    public function getApprove();

    /**
     * Set Product Image
     * @param $productId
     * @return void
     */
    public function setProductImage($productId);

    /**
     * Get Product Image
     * @return string
     */
    public function getProductImage();

    /**
     * Set Link Image
     * @param string $linkImage
     * @return string
     */
    public function setLinkImage($linkImage);

    /**
     * Get Link Image
     * @return string
     */
    public function getLinkImage();

    /**
     * setEmailCustomer
     * @param string $emailCustomer
     * @return void
     */
    public function setEmailCustomer($emailCustomer);

    /**
     * GetEmailCustomer
     * @return string
     */
    public function getEmailCustomer();

    /**
     * Set Store View
     * @param int $storeId
     * @return void
     */
    public function setStoreView($storeId);

    /**
     * Get Store View
     * @return int
     */
    public function getStoreView();
}
