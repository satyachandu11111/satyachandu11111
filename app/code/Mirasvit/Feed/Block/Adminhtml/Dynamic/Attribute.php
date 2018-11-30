<?php

namespace Mirasvit\Feed\Block\Adminhtml\Dynamic;

use Magento\Backend\Block\Widget\Grid\Container;

class Attribute extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_dynamic_attribute';
        $this->_blockGroup = 'Mirasvit_Feed';
        $this->_headerText = __('Manage Dynamic Attributes');
        $this->_addButtonLabel = __('Add Attribute');

        parent::_construct();
    }
}
