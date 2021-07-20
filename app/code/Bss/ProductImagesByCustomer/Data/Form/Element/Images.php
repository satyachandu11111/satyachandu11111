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
namespace Bss\ProductImagesByCustomer\Data\Form\Element;

use Magento\Framework\Escaper;
use \Magento\Framework\App\ProductMetadataInterface;

class Images extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     *
     * Layout
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * Version
     * @var ProductMetadataInterface
     */
    protected $version;

    /**
     * Images constructor.
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        ProductMetadataInterface $version,
        Escaper $escaper,
        \Magento\Framework\View\LayoutInterface $layout,
        $data = []
    ) {
        $this->version = $version;
        $this->layout = $layout;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * Get magento version
     * @return string
     */
    protected function getMagentoVersion()
    {
        return $this->version->getVersion();
    }

    /**
     * Conpare Version
     * @param $version
     * @return bool
     */
    protected function compareVersion($version)
    {
        $versionCurrent = $this->getMagentoVersion();
        if (
            version_compare($versionCurrent, $version) === 1 ||
            version_compare($versionCurrent, $version) === 0)
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Element Html
     * @return string
     */
    public function getElementHtml()
    {

        //Check version greater 2.0.17 run phtml, Check version not greater 2.0.17 run diffirent phtml
        if ($this->compareVersion("2.1.0") === true) {
            return $this->layout->createBlock(
                'Bss\ProductImagesByCustomer\Block\Adminhtml\Image\Edit\Tab\Images'
            )->setTemplate('Bss_ProductImagesByCustomer::helper/gallery.phtml')->toHtml();
        } else {
            return $this->layout->createBlock(
                'Bss\ProductImagesByCustomer\Block\Adminhtml\Image\Edit\Tab\Images'
            )->setTemplate('Bss_ProductImagesByCustomer::helper/subGallery.phtml')->toHtml();
        }

    }
}
