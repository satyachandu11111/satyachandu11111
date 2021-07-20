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
namespace Bss\ProductImagesByCustomer\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Editor extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Wysiwyg Config
     *
     * @var WysiwygConfig
     */
    protected $wysiwygConfig;

    /**
     * Construct
     * @param Context       $context
     * @param WysiwygConfig $wysiwygConfig
     * @param array         $data
     */
    public function __construct(
        Context $context,
        WysiwygConfig $wysiwygConfig,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    /**
     * Get Element Html
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        //Config Editor
        $config['document_base_url'] = $this->getData('store_media_url');
        $config['store_id'] = $this->getData('store_id');
        $config['add_variables'] = false;
        $config['add_widgets'] = false;
        $config['add_directives'] = true;
        $config['use_container'] = true;
        $config['container_class'] = 'hor-scroll';

        // set wysiwyg for element
        $element->setWysiwyg(true);

        // set configuration values
        $element->setConfig($this->wysiwygConfig->getConfig($config));

        return parent::_getElementHtml($element);
    }
}
