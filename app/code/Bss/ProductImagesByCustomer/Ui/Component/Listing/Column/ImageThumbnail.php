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
namespace Bss\ProductImagesByCustomer\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\UrlInterface;
use \Magento\Framework\DataObjectFactory;

class ImageThumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * NAME
     */
    const NAME = 'link_image';

    /**
     * ALT_FIELD
     */
    const ALT_FIELD = 'name';

    /**
     * Data Object
     * @var DataObjectFactory
     */
    protected $dataObject;

    /**
     * Store manager
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        ContextInterface $context,
        DataObjectFactory $dataObject,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->dataObject = $dataObject;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $image = $this->dataObject->create($item);
                $mediaRelativePath = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $imagePath = $mediaRelativePath."bss/productimagesbycustomer/".$item['link_image'];
                $item[$fieldName . '_src'] = $imagePath;
                $item[$fieldName . '_alt'] = $this->getAlt($item);
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'bss_productimagesbycustomer_admin/image/edit',
                    ['bss_image_customer_upload_id' => $item["bss_image_customer_upload_id"]]
                );
                $item[$fieldName . '_orig_src'] = $imagePath;
            }
        }
        return $dataSource;
    }

    /**
     * @param array $row
     *
     * @return null|string
     */
    protected function getAlt($row)
    {
        $altField = self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
    }
}
