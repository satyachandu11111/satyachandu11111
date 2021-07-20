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
namespace Bss\ProductImagesByCustomer\Model\ResourceModel\Image;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Id Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'bss_image_customer_upload_id';

    /**
     * Construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Bss\ProductImagesByCustomer\Model\Image',
            'Bss\ProductImagesByCustomer\Model\ResourceModel\Image'
        );
        $this->_map['fields']['bss_image_customer_upload_id'] = 'main_table.bss_image_customer_upload_id';
    }
}
